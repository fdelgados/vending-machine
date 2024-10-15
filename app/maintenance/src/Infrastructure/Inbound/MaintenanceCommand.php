<?php declare(strict_types=1);

namespace VendingMachine\Maintenance\Infrastructure\Inbound;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use VendingMachine\Common\Application\ListProducts\ListProductsService;
use VendingMachine\Common\Application\ListProducts\ProductDto;
use VendingMachine\Common\Application\ListProducts\ProductMap;
use VendingMachine\Maintenance\Application\ReplenishChange\ReplenishChangeCommand;
use VendingMachine\Maintenance\Application\ReplenishChange\ReplenishChangeService;
use VendingMachine\Maintenance\Application\Restock\RestockCommand;
use VendingMachine\Maintenance\Application\Restock\RestockService;
use VendingMachine\Maintenance\Application\ViewChange\CoinDto;
use VendingMachine\Maintenance\Application\ViewChange\CoinMap;
use VendingMachine\Maintenance\Application\ViewChange\ViewChangeService;

final class MaintenanceCommand extends Command
{
    private const string DEFAULT_NAME = 'vending-machine:maintenance';

    public function __construct(
        private readonly ListProductsService $listProductsService,
        private readonly RestockService $restockService,
        private readonly ViewChangeService $viewChangeService,
        private readonly ReplenishChangeService $replenishChangeService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::DEFAULT_NAME)
            ->setDescription('Maintenance the vending machine');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Welcome to maintenance system!');

        $input->setInteractive(true);

        do {
            $continue = $this->operate($input, $output);
        } while ($continue);

        return Command::SUCCESS;
    }

    private function operate(InputInterface $input, OutputInterface $output): bool
    {
        $io = new SymfonyStyle($input, $output);
        $choices = [
            '1' => 'Restock products',
            '2' => 'Replenish change',
            'x' => 'Exit',
        ];

        $question = new ChoiceQuestion('Please select an option:', $choices, 'x');

        $question->setErrorMessage('Invalid option [%s]');
        $option = $io->askQuestion($question);

        return match ($option) {
            'x' => false,
            '1' => $this->restockProducts($input, $output),
            '2' => $this->replenishChange($input, $output),
        };
    }

    private function restockProducts(InputInterface $input, OutputInterface $output): bool
    {
        $io = new SymfonyStyle($input, $output);

        do {
            $products = $this->listProductsService->list();

            $this->printProductList($input, $output, $products);

            $choices = $products->map(fn (ProductDto $product, $id) => $product->getName())->toArray();
            $choices['x'] = 'Exit';

            $question = new ChoiceQuestion('Please select an option:', $choices, 'x');

            $question->setErrorMessage('Invalid option [%s]');
            $option = $io->askQuestion($question);
            $this->restock($input, $output, $products->get($option));
        } while ($option != 'x');

        return true;
    }

    private function restock(InputInterface $input, OutputInterface $output, ?ProductDto $product): void
    {
        if ($product === null) {
            return;
        }

        $io = new SymfonyStyle($input, $output);

        $io->section(sprintf('Restocking %s. Current quatity: %s', $product->getName(), $product->getQuantity()));

        $quantity = $io->ask(
            'Quantity', null,
            function ($quantity) {
                if (!is_numeric($quantity) || $quantity < 1) {
                    throw new \RuntimeException('Quantity must be a number greater than 0');
                }

                return (int) $quantity;
            }
        );

        $result = $this->restockService->restock(new RestockCommand($product->getId(), $quantity));

        if ($result->isFailure()) {
            $io->error($result->getErrorMessage());
        }
    }

    private function printProductList(InputInterface $input, OutputInterface $output, ProductMap $products): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Restock products');
        $rows = $products->map(function (ProductDto $product) {
            return [
                $product->getId(),
                $product->getName(),
                $product->getPrice(),
                $product->getQuantity(),
            ];
        });

        $io->table(['ID', 'Name', 'Price', 'Stock'], $rows->toArray());
    }

    private function replenishChange(InputInterface $input, OutputInterface $output): bool
    {
        $io = new SymfonyStyle($input, $output);

        do {
            $change = $this->viewChangeService->view();

            $this->printAvailableCoins($input, $output, $change);

            $choices = $change->map(fn (CoinDto $coinDto, $id) => $coinDto->getValue())->toArray();
            $choices['x'] = 'Exit';

            $question = new ChoiceQuestion('Please select an option:', $choices, 'x');

            $question->setErrorMessage('Invalid option [%s]');
            $option = $io->askQuestion($question);

            $this->replenish($input, $output, $change->get($option));
        } while ($option != 'x');

        return true;
    }

    private function printAvailableCoins(InputInterface $input, OutputInterface $output, CoinMap $coins): void
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Replenish the change');
        $rows = $coins->map(fn (CoinDto $coin) => [$coin->getValue(), $coin->getQuantity()]);

        $io->table(['Value', 'Quantity'], $rows->toArray());
    }

    private function replenish(InputInterface $input, OutputInterface $output, ?CoinDto $coin): void
    {
        if ($coin === null) {
            return;
        }

        $io = new SymfonyStyle($input, $output);

        $io->section(sprintf('Replenish %s coins. Current quantity: %s', $coin->getValue(), $coin->getQuantity()));

        $quantity = $io->ask(
            'Quantity', null,
            function ($quantity) {
                if (!is_numeric($quantity) || $quantity < 1) {
                    throw new \RuntimeException('Quantity must be a number greater than 0');
                }

                return (int) $quantity;
            }
        );

        $this->replenishChangeService->replenish(new ReplenishChangeCommand($coin->getValue(), $quantity));
    }
}
