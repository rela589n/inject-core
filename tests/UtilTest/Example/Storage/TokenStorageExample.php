<?php

declare(strict_types=1);

namespace Rela589n\Injection\Test\UtilTest\Example\Storage;

use Rela589n\Injection\Test\UtilTest\Example\TokenExample;

interface TokenStorageExample
{
    public function generateToken(): TokenExample;
}
