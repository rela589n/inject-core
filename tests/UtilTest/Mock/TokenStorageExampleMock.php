<?php

declare(strict_types=1);

namespace Tests\UtilTest\Mock;

use DateInterval;
use DateTimeImmutable;
use Tests\UtilTest\Example\Storage\TokenStorageExample;
use Tests\UtilTest\Example\TokenExample;

final class TokenStorageExampleMock implements TokenStorageExample
{
    public $expiryDate;

    public function generateToken(): TokenExample
    {
        return new TokenExample(
            'test_secret',
            'test_public',
            $this->expiryDate = (new DateTimeImmutable())->add(DateInterval::createFromDateString('1 second'))
        );
    }
}
