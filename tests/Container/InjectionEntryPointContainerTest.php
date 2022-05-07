<?php

declare(strict_types=1);

namespace Tests\Container;

use Inject\Container\Exception\ContainerNotSetException;
use Inject\Container\InjectionEntryPointContainer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Container\Mock\InjectionContainerMock;
use Tests\Container\Mock\InjectionContainerWrapperMock;
use Tests\Util\ContainerTearDownTrait;
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
