<?php

declare(strict_types=1);

namespace Inject\Container;

interface InjectionContainerWrapper extends InjectionContainer
{
    /**
     * This method should accept already registered container
     * and fall back to it if not able to resolve dependency by itself.
     */
    public function wrapContainer(InjectionContainer $wrapped): void;
}
