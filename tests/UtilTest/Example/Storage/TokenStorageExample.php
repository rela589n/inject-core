<?php

declare(strict_types=1);

namespace Tests\UtilTest\Example\Storage;

use Tests\UtilTest\Example\TokenExample;

interface TokenStorageExample
{
    public function generateToken(): TokenExample;
}
