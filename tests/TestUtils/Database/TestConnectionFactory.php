<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\TestUtils\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database as DatabaseAdapter;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Tests\TestUtils\Database\FixtureLoader;
use Webmozart\PathUtil\Path;

class TestConnectionFactory
{
    public static function get(): Connection
    {
        // Use facts to determine the database, for poc we use CE
        $connectionParams = [
            'dbname' => 'testce',
            'user' => 'oxid',
            'password' => 'oxid',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ];
        $connection = DriverManager::getConnection($connectionParams);

        $fixtureLoader = new FixtureLoader($connection);
        $fixtureLoader->loadFixtures([Path::join(__DIR__, 'basic_fixtures.yaml')]);

        try {
            $connection->executeQuery("SELECT 1 FROM oxv_oxarticles");
        } catch (\Exception $e) {
            (new ViewsGenerator())->generate();
        }

        self::setConnectionForLegacyCode($connection);

        return $connection;

    }

    private static function setConnectionForLegacyCode(Connection $connection) {

        $databaseAdapter = new DatabaseAdapter();
        $refObject   = new \ReflectionObject( $databaseAdapter );
        $refProperty = $refObject->getProperty( 'connection' );
        $refProperty->setAccessible( true );
        $refProperty->setValue($databaseAdapter, $connection);

        $databaseProvider = DatabaseProvider::getInstance();
        $refObject   = new \ReflectionObject( $databaseProvider );
        $refProperty = $refObject->getProperty( 'db' );
        $refProperty->setAccessible( true );
        $refProperty->setValue($databaseAdapter);

        $connection->executeQuery("SET @@SESSION.sql_mode=''");

    }
}