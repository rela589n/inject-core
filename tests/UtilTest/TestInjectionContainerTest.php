<?php

declare(strict_types=1);

namespace Tests\UtilTest;

use PHPUnit\Framework\TestCase;
use Tests\Util\ContainerTearDownTrait;
use Tests\Util\TestContainerSetUpTrait;
use Tests\UtilTest\Example\GeneratedTokenExample;
use Tests\UtilTest\Mock\TokenStorageExampleMock;

use function sleep;

final class TestInjectionContainerTest extends TestCase
{
    use TestContainerSetUpTrait {
        setUp as setUpContainer;
    }
    use ContainerTearDownTrait;

    /** @var TokenStorageExampleMock */
    private $tokenStorageMock;

    protected function setUp(): void
    {
        $this->setUpContainer();

        $this->tokenStorageMock = new TokenStorageExampleMock();
        $this->testContainer
            ->registerInjectorFn(
                GeneratedTokenExample::class,
                function (GeneratedTokenExample $token) {
                    $token->__inject($this->tokenStorageMock);
                }
            );
    }

    public function test(): void
    {
        $token = new GeneratedTokenExample();

        self::assertSame('test_public', $token->publicKey());
        self::assertSame('test_secret', $token->secretKey());
        self::assertSame($this->tokenStorageMock->expiryDate, $token->validUntil());
        sleep(1);
        self::assertNotSame($this->tokenStorageMock->expiryDate, $token->validUntil());
    }
}
