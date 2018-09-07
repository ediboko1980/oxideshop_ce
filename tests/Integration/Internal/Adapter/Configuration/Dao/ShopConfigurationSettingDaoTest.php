<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Adapter\Configuration\Dao;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopConfigurationSettingDaoTest extends TestCase
{
    /**
     * @dataProvider settingValueDataProvider
     */
    public function testSettingSaving(string $name, $value)
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting(
            1,
            $name,
            $value
        );

        $settingDao->save($shopConfigurationSetting);

        $this->assertEquals(
            $shopConfigurationSetting,
            $settingDao->get($name, 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException
     */
    public function testGetNonExistentSetting()
    {
        $settingDao = $this->getConfigurationSettingDao();

        $settingDao->get('onExistentSetting', 1);
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     */
    public function testSettingSavingCompatibility(string $name, $value)
    {
        $settingDao = $this->getConfigurationSettingDao();

        $shopConfigurationSetting = new ShopConfigurationSetting(
            1,
            $name,
            $value
        );

        $settingDao->save($shopConfigurationSetting);

        $this->assertEquals(
            $value,
            Registry::getConfig()->getShopConfVar($name, 1)
        );
    }

    public function settingValueDataProvider()
    {
        return [
            [
                'string',
                'testString',
            ],
            [
                'int',
                1,
            ],
            [
                'bool',
                true,
            ],
            [
                'array',
                [
                    'element'   => 'value',
                    'element2'  => 'value',
                ]
            ],
        ];
    }

    private function getConfigurationSettingDao()
    {
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $settingDaoDefinition = $container->getDefinition(ShopConfigurationSettingDaoInterface::class);
        $settingDaoDefinition->setPublic(true);

        $container->setDefinition(
            ShopConfigurationSettingDaoInterface::class,
            $settingDaoDefinition
        );

        $container->compile();

        return $container->get(ShopConfigurationSettingDaoInterface::class);
    }
}