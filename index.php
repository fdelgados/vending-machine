<?php declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use DI\ContainerBuilder;
use Symfony\Component\Console\Application;
use VendingMachine\Operation\Infrastructure\Inbound\Console\OperationCommand;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config/di.php');
$container = $containerBuilder->build();

$application = new Application();

$application->add($container->get(OperationCommand::class));

$application->run();