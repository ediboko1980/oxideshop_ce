<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Internal\Password\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class PasswordServiceBridgeTest extends TestCase
{
    use ContainerTrait;

    /**
     * End-to-end test for the password hashing service.
     */
    public function testGetPasswordHashServiceReturnsWorkingPasswordHashServiceBcrypt()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordHashService = $passwordServiceBridge->getPasswordHashService(PASSWORD_BCRYPT);
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertInstanceOf(PasswordHashBcryptService::class, $passwordHashService);
        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     * End-to-end test for the password verification service.
     */
    public function testGetPasswordVerificationServiceReturnsWorkingService()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordVerificationService = $passwordServiceBridge->getPasswordVerificationService();

        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->assertTrue(
            $passwordVerificationService->verifyPassword($password, $passwordHash)
        );
    }
}