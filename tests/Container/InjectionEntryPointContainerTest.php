<?php

declare(strict_types=1);

namespace Rela589n\Injection\Test\Container;

use Rela589n\Injection\Container\Exception\ContainerNotSetException;
use Rela589n\Injection\Container\InjectionEntryPointContainer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Rela589n\Injection\Test\Container\Mock\InjectionContainerMock;
use Rela589n\Injection\Test\Container\Mock\InjectionContainerWrapperMock;
use Rela589n\Injection\Test\Util\ContainerTearDownTrait;
use Webmozart\Assert\InvalidArgumentException;

/** @covers InjectionEntryPointContainer */
final class InjectionEntryPointContainerTest extends TestCase
{
    use ContainerTearDownTrait;

    /** @var InjectionEntryPointContainer */
    private $container;

    /** @var InjectionContainerMock */
    private $containerMock;

    /** @var InjectionContainerWrapperMock */
    private $containerWrapperMock;

    protected function setUp(): void
    {
        $this->container = InjectionEntryPointContainer::getSelf();
        $this->containerMock = new InjectionContainerMock();
        $this->containerWrapperMock = new InjectionContainerWrapperMock();
    }

    public function testFailsInjectionIfNoContainerSet(): void
    {
        $this->expectException(ContainerNotSetException::class);

        $this->container->injectTo(new stdClass());
    }

    public function testWrapsRootLevelContainer(): void
    {
        $this->container->wrapContainer($this->containerMock);

        $object = new stdClass();
        $this->container->injectTo($object);

        self::assertSame($object, $this->containerMock->lastInject);
    }

    public function testRequiresContainerWrapperForNotRootLevelContainers(): void
    {
        $this->container->wrapContainer($this->containerMock);

        $this->expectException(InvalidArgumentException::class);

        $this->container->wrapContainer($this->containerMock);
    }

    public function testWrapsNonRootContainer(): void
    {
        $this->container->wrapContainer($this->containerMock);
        $this->container->wrapContainer($this->containerWrapperMock);

        self::assertSame($this->containerMock, $this->containerWrapperMock->wrapped);

        $object = new stdClass();
        $this->container->injectTo($object);

        self::assertSame($object, $this->containerWrapperMock->lastInject);
        self::assertSame($object, $this->containerMock->lastInject);
    }
}
