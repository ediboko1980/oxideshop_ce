<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\TestUtils\Traits;

use OxidEsales\EshopCommunity\Tests\TestUtils\TestContainerFactory;
use Symfony\Component\DependencyInjection\Container;

/**
 * @internal
 * @mixin Container
 */
trait ContainerTrait
{
    private $container;

    private $serviceMocks = [];

    public function overrideService(string $serviceKey, $mock)
    {
        $this->serviceMocks[$serviceKey] = $mock;
    }

    protected function get(string $serviceId)
    {
        if ($this->container === null) {
            $this->container = (new TestContainerFactory())->create($this->serviceMocks);
            $this->container->compile();
            $this->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')->generate();
        }

        return $this->container->get($serviceId);
    }
}
