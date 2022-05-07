<?php

declare(strict_types=1);

namespace Rela589n\Injection\Test\UtilTest\Example;

use DateTimeInterface;
use Rela589n\Injection\Test\UtilTest\Example\Storage\TokenStorageExample;

use function inject;

final class GeneratedTokenExample
{
    /** @var TokenStorageExample */
    private $storage;

    /** @var ?TokenExample */
    private $token;

    public function __inject(TokenStorageExample $storage): void
    {
        $this->storage = $storage;
    }

    public function __construct()
    {
        inject($this);
    }

    private function token(): TokenExample
    {
        if (null === $this->token || $this->token->isExpired()) {
            $this->token = $this->storage->generateToken();
        }

        return $this->token;
    }

    public function secretKey(): string
    {
        return $this->token()->secretKey();
    }

    public function publicKey(): string
    {
        return $this->token()->publicKey();
    }

    public function validUntil(): DateTimeInterface
    {
        return $this->token()->expiresAt();
    }
}
