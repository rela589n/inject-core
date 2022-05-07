<?php

declare(strict_types=1);

namespace Rela589n\Injection\Test\UtilTest\Mock;

use DateInterval;
use DateTimeImmutable;
use Rela589n\Injection\Test\UtilTest\Example\Storage\TokenStorageExample;
use Rela589n\Injection\Test\UtilTest\Example\TokenExample;

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
