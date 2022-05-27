<?php

use Rela589n\RuntimeInjection\Container\InjectionEntryPointContainer;

if (!function_exists('inject')) {
    function inject(object $injectable): void
    {
        $container = InjectionEntryPointContainer::getSelf();
        $container->injectTo($injectable);
    }
}
