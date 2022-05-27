<?php

declare(strict_types=1);

namespace Rela589n\RuntimeInjection\Test\Util;

use Rela589n\RuntimeInjection\Container\InjectionEntryPointContainer;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @see TestCase */
trait ContainerTearDownTrait
{
    protected function tearDown(): void
    {
        $containerProperty = new ReflectionProperty(InjectionEntryPointContainer::class, 'container');
        $containerProperty->setAccessible(true);
        $containerProperty->setValue(InjectionEntryPointContainer::getSelf(), null);
    }
}
