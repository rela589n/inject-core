<?php

declare(strict_types=1);

namespace Rela589n\RuntimeInjection\Test\UtilTest\Example\Storage;

use Rela589n\RuntimeInjection\Test\UtilTest\Example\TokenExample;

interface TokenStorageExample
{
    public function generateToken(): TokenExample;
}
