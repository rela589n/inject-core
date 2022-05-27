<?php

declare(strict_types=1);

namespace Rela589n\RuntimeInjection\Test\UtilTest\Example;

use DateTime;
use DateTimeInterface;

final class TokenExample
{
    /** @var DateTimeInterface */
    private $expiresAt;

    /** @var string */
    private $secret;

    /** @var string */
    private $public;

    public function __construct(string $secret, string $public, DateTimeInterface $expiresAt)
    {
        $this->secret = $secret;
        $this->public = $public;
        $this->expiresAt = $expiresAt;
    }

    public function secretKey(): string
    {
        return $this->secret;
    }

    public function publicKey(): string
    {
        return $this->public;
    }

    public function expiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return new DateTime() >= $this->expiresAt;
    }
}
