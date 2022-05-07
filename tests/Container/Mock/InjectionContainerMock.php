<?php

declare(strict_types=1);

namespace Tests\Container\Mock;

use Inject\Container\InjectionContainer;

final class InjectionContainerMock implements InjectionContainer
{
    public $lastInject;

    public function injectTo(object $injectable): void
    {
        $this->lastInject = $injectable;
    }
}
