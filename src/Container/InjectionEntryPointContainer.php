<?php

declare(strict_types=1);

namespace Inject\Container;

use Inject\Container\Exception\ContainerNotSetException;
use Webmozart\Assert\Assert;

final class InjectionEntryPointContainer implements InjectionContainerWrapper
{
    /** @var self */
    private static $self;

    /** @var ?InjectionContainer */
    private $container;

    public static function getSelf(): self
    {
        if (null === self::$self) {
            self::$self = new self();
        }

        return self::$self;
    }

    /** @param  InjectionContainer|InjectionContainerWrapper  $wrapped */
    public function wrapContainer(InjectionContainer $wrapped): void
    {
        if (null !== $this->container) {
            Assert::isInstanceOf($wrapped, InjectionContainerWrapper::class);

            $wrapped->wrapContainer($this->container);
        }

        $this->container = $wrapped;
    }

    public function injectTo(object $injectable): void
    {
        if (null === $this->container) {
            throw new ContainerNotSetException($injectable);
        }

        $this->container->injectTo($injectable);
    }
}
