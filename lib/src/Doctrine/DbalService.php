<?php declare(strict_types=1);

namespace VendingMachine\Lib\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Abstract class DbalService
 *
 * This class provides a base for database services using Doctrine DBAL.
 * It initializes a database connection and provides a method to get a query builder.
 */
abstract class DbalService
{
    /**
     * @var Connection The database connection instance.
     */
    protected Connection $connection;

    /**
     * DbalService constructor.
     *
     * Initializes the database connection using parameters from environment variables.
     */
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

    /**
     * Get a new QueryBuilder instance.
     *
     * @return QueryBuilder A new QueryBuilder instance.
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}
