<?php

declare(strict_types=1);

namespace Rela589n\Injection\Test\Container\Mock;

use Rela589n\Injection\Container\InjectionContainer;

final class InjectionContainerMock implements InjectionContainer
{
    public $lastInject;

    public function injectTo(object $injectable): void
    {
        $this->lastInject = $injectable;
    }
}
