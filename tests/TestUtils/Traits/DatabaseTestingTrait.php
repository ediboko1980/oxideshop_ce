<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\TestUtils\Traits;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Tests\TestUtils\Database\FixtureLoader;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\ContainerTrait;

trait DatabaseTestingTrait
{
    use Traits\ContainerTrait;

    /** @var FixtureLoader */
    private $fixtureLoader = null;

    public function setupTestDatabase()
    {
        $this->get(Connection::class);
    }
    public function loadFixtures(array $fixtureFiles) {
        $this->getFixtureLoader()->loadFixtures($fixtureFiles);
    }

    public function loadFixture($fixtureFile) {
        $this->loadFixtures([$fixtureFile]);
    }

    public function cleanupFixtureTables() {
        $this->getFixtureLoader()->cleanupFixtureTables();
    }

    public function cleanupTable($tablename) {
        $this->getFixtureLoader()->cleanupTable($tablename);
    }

    private function getFixtureLoader()
    {
        if ($this->fixtureLoader == null) {
            $this->fixtureLoader = new FixtureLoader($this->get(Connection::class));
        }
        return $this->fixtureLoader;
    }

}