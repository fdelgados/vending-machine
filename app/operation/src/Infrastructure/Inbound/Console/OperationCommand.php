<?php declare(strict_types=1);

namespace VendingMachine\Operation\Infrastructure\Inbound\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use VendingMachine\Common\Application\ListProducts\ListProductsService;
use VendingMachine\Common\Application\ListProducts\ProductDto;
use VendingMachine\Operation\Application\AddCredit\AddCreditService;
use VendingMachine\Operation\Application\AddCredit\AddCreditCommand;
use VendingMachine\Operation\Application\CancelSale\CancelCommand;
use VendingMachine\Operation\Application\CancelSale\CancelSaleService;
use VendingMachine\Operation\Application\Purchase\PurchaseCommand;
use VendingMachine\Operation\Application\Purchase\PurchaseService;

final class OperationCommand extends Command
{
    private const string DEFAULT_NAME = 'vending-machine:operate';

    public function __construct(
        private readonly AddCreditService $addCreditService,
        private readonly CancelSaleService $cancelSaleService,
        private readonly PurchaseService $purchaseService,
        private readonly ListProductsService $listProductsService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::DEFAULT_NAME)
            ->setDescription('Operate the vending machine');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Welcome to the vending machine!');

        $input->setInteractive(true);

        do {
            $continue = $this->operate($input, $output);
        } while ($continue);

        return Command::SUCCESS;
    }

    private function operate(InputInterface $input, OutputInterface $output): bool
    {
        $io = new SymfonyStyle($input, $output);

        $saleId = $this->insertCoin($input, $output);

        while (true) {
            $productId = $this->selectProduct($input, $output);

            if ($productId === null) {
                $this->cancelSale($input, $output, $saleId);

                return true;
            }

            $purchaseCommand = new PurchaseCommand($saleId, $productId);
            $result = $this->purchaseService->purchase($purchaseCommand);

            if ($result->isFailure()) {
                if ($result->errorIs('product_out_of_stock')) {
                    $io->error('The product is out of stock. Please select another product.');

                    continue;
                }

                if ($result->errorIs('insufficient_credit')) {
                    $io->error('Insufficient credit. Please insert more coins.');

                    $saleId = $this->insertCoin($input, $output, $saleId);

                    continue;
                }

                $io->error($result->getErrorMessage());

                $this->cancelSale($input, $output, $saleId);

                return true;
            }

            $io->success((string) $result->getValue());

            return $this->askToContinue($input, $output);
        }
    }

    private function insertCoin(InputInterface $input, OutputInterface $output, ?string $saleId = null): string
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');
        $question = new Question('Insert coins (separated by commas): ');
        $question->setValidator(function ($answer) {
            $values = array_map(
                fn ($value) => floatval(trim($value)),
                explode(',', $answer)
            );

            if (count($values) === 0) {
                throw new \RuntimeException('You must enter at least one value.');
            }

            return $values;
        });


        $coins = $helper->ask($input, $output, $question);

        $addCreditCommand = new AddCreditCommand($saleId, ...$coins);
        $result = $this->addCreditService->add($addCreditCommand);

        $io->info('Credit: '. $result->getCredit());

        return $result->getSaleId();
    }

    private function selectProduct(InputInterface $input, OutputInterface $output): ?string
    {
        $io = new SymfonyStyle($input, $output);

        $products = $this->listProductsService->list();

        $choices = $products->map(fn (ProductDto $product, $id) => $product->__toString())->toArray();

        $choices['x'] = 'Return money';

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Select an option:', $choices);

        $question->setErrorMessage('Selection %s is invalid.');
        $product = $helper->ask($input, $output, $question);

        $io->info('Your selection: ' . $choices[$product]);

        return $product === 'x' ? null : (string) $product;
    }

    private function cancelSale(InputInterface $input, OutputInterface $output, string $saleId): void
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->cancelSaleService->cancel(new CancelCommand($saleId));

        $io->success('Your money: ' . $result->getValue());
    }

    private function askToContinue(InputInterface $input, OutputInterface $output): bool
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Would you like to select another drink?',
            ['y' => 'Yes', 'n' => 'No'],
            'n'
        );
        $answer = $helper->ask($input, $output, $question);

        return $answer === 'y';
    }
}
