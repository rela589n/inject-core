<?php

declare(strict_types=1);

namespace Tests\Util;

use Closure;
use Inject\Container\InjectionContainer;
use Inject\Container\InjectionContainerWrapper;
use RuntimeException;

use function get_class;
use function get_parent_class;

final class TestInjectionContainer implements InjectionContainerWrapper
{
    /** @var ?InjectionContainer */
    private $wrapped;

    /** @psalm-var array<class-string,Closure> */
    private $injectionFunctions = [];

    /** @psalm-param class-string $className */
    public function registerInjectorFn(string $className, Closure $injector): void
    {
        $this->injectionFunctions[$className] = $injector;
    }

    public function wrapContainer(InjectionContainer $wrapped): void
    {
        $this->wrapped = $wrapped;
    }

    public function injectTo(object $injectable): void
    {
        $inject = $this->findInjector($injectable);
        $inject($injectable);
    }

    private function findInjector(object $injectable): Closure
    {
        $injector = $this->findInjectorForClass($class = get_class($injectable));
        if (null === $injector) {
            throw new RuntimeException("No injector was registered for $class. Please, use registerInjectorFn() to do it.");
        }

        return $injector;
    }

    /** @psalm-param class-string $className */
    private function findInjectorForClass(string $className): ?Closure
    {
        if (isset($this->injectionFunctions[$className])) {
            return $this->injectionFunctions[$className];
        }

        $parent = get_parent_class($className);
        if ($parent) {
            return $this->findInjectorForClass($parent);
        }

        if (null !== $this->wrapped) {
            return Closure::fromCallable([$this->wrapped, 'injectTo']);
        }

        return null;
    }
}
