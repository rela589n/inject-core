<?php

declare(strict_types=1);

namespace Inject\Container;

interface InjectionContainer
{
    public function injectTo(object $injectable): void;
}
