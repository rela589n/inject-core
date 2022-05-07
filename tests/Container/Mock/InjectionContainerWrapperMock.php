<?php

declare(strict_types=1);

namespace Rela589n\Injection\Test\Container\Mock;

use Rela589n\Injection\Container\InjectionContainer;
use Rela589n\Injection\Container\InjectionContainerWrapper;

final class InjectionContainerWrapperMock implements InjectionContainerWrapper
{
    /** @var InjectionContainer */
    public $wrapped;
    public $lastInject;

    public function wrapContainer(InjectionContainer $wrapped): void
    {
        $this->wrapped = $wrapped;
    }

    public function injectTo(object $injectable): void
    {
        $this->lastInject = $injectable;
        $this->wrapped->injectTo($injectable);
    }
}
