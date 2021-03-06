<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

interface TransactionServiceInterface
{
    /**
     * Initiates a transaction.
     */
    public function begin();

    /**
     * Commits a transaction.
     */
    public function commit();

    /**
     * Rolls back the current transaction.
     */
    public function rollback();
}
