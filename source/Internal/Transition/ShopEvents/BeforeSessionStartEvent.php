<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use Symfony\Contracts\EventDispatcher\Event;

class BeforeSessionStartEvent extends Event
{
    const NAME = self::class;
}
