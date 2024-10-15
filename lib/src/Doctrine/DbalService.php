<?php declare(strict_types=1);

namespace VendingMachine\Lib\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class DbalService
{
    protected Connection $connection;

    public function __construct()
    {
        $connectionParams = [
            'dbname'   => getenv('DB_NAME'),
            'user'     => getenv('DB_USER') ?? 'root',
            'password' => getenv('DB_PASSWORD'),
            'host'     => getenv('DB_HOST'),
            'driver'   => 'pdo_mysql',
            'port'     => 3306,
        ];

        $this->connection = DriverManager::getConnection($connectionParams);
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}
