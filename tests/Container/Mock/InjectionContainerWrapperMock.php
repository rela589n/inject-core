<?php

declare(strict_types=1);

namespace Tests\Container\Mock;

use Inject\Container\InjectionContainer;
use Inject\Container\InjectionContainerWrapper;

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
