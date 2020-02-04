<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\Configuration\SmartyConfigurationFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContext;
use OxidEsales\EshopCommunity\Internal\Framework\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\ConfigHandlingTrait;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\DatabaseTestingTrait;

class SmartyBuilderTest extends \PHPUnit\Framework\TestCase
{
    use ConfigHandlingTrait;
    use DatabaseTestingTrait;

    public function setUp()
    {
        parent::setUp();
        $this->backupConfig();
    }

    public function tearDown()
    {
        $this->restoreConfig();
        parent::tearDown();
    }

    /**
     * @dataProvider smartySettingsDataProvider
     *
     * @param bool $securityMode
     * @param array $smartySettings
     */
    public function testSmartySettingsAreSetCorrect($securityMode, $smartySettings)
    {
        $smartyBuilder = new SmartyBuilder();
        $this->setupAndConfigureContainer($securityMode);
        $configurationFactory = $this->get(SmartyConfigurationFactoryInterface::class);
        $configuration = $configurationFactory->getConfiguration();
        $smarty = $smartyBuilder->setSettings($configuration->getSettings())
            ->setSecuritySettings($configuration->getSecuritySettings())
            ->registerPlugins($configuration->getPlugins())
            ->registerPrefilters($configuration->getPrefilters())
            ->registerResources($configuration->getResources())
            ->getSmarty();

        foreach ($smartySettings as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals($varValue, $smarty->$varName, 'Not correct value of the smarts setting: ' . $varName);
        }
    }

    /**
     * @return array
     */
    public function smartySettingsDataProvider()
    {
        return [
            'security on' => [1, $this->getSmartySettingsWithSecurityOn()],
            'security off' => [0, $this->getSmartySettingsWithSecurityOff()]
        ];
    }

    private function getSmartySettingsWithSecurityOn(): array
    {
        $config = Registry::getConfig();
        $templateDirs = Registry::getUtilsView()->getTemplateDirs();
        return [
            'security' => true,
            'php_handling' => 2,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => $templateDirs,
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'security_settings' => [
                'PHP_HANDLING' => false,
                'IF_FUNCS' =>
                    [
                        0 => 'array',
                        1 => 'list',
                        2 => 'isset',
                        3 => 'empty',
                        4 => 'count',
                        5 => 'sizeof',
                        6 => 'in_array',
                        7 => 'is_array',
                        8 => 'true',
                        9 => 'false',
                        10 => 'null',
                        11 => 'XML_ELEMENT_NODE',
                        12 => 'is_int',
                    ],
                'INCLUDE_ANY' => false,
                'PHP_TAGS' => false,
                'MODIFIER_FUNCS' =>
                    [
                        0 => 'count',
                        1 => 'round',
                        2 => 'floor',
                        3 => 'trim',
                        4 => 'implode',
                        5 => 'is_array',
                        6 => 'getimagesize',
                    ],
                'ALLOW_CONSTANTS' => true,
                'ALLOW_SUPER_GLOBALS' => true,
            ],
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartySettingsWithSecurityOff(): array
    {
        $config = Registry::getConfig();
        $templateDirs = Registry::getUtilsView()->getTemplateDirs();
        return [
            'security' => false,
            'php_handling' => $config->getConfigParam('iSmartyPhpHandling'),
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir') . "/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => $templateDirs,
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartyPlugins()
    {
        return array_merge(Registry::getUtilsView()->getSmartyPluginDirectories(), ['plugins']);
    }

    private function getSmartyContext($securityMode = false): SmartyContext
    {
        $config = Registry::getConfig();
        $config->setConfigParam('blDemoShop', $securityMode);
        $config->setConfigParam('iDebug', 0);

        return new SmartyContext(new BasicContext(), $config, Registry::getUtilsView());
    }

    /**
     * We need to replace services in the container with a mock
     *
     * @param bool $securityMode
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function setupAndConfigureContainer($securityMode = false)
    {
        $this->overrideService(SmartyContextInterface::class, $this->getSmartyContext($securityMode));
    }
}
