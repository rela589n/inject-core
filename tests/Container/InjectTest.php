<?php

declare(strict_types=1);

namespace Rela589n\RuntimeInjection\Test\Container;

use Rela589n\RuntimeInjection\Container\InjectionEntryPointContainer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Rela589n\RuntimeInjection\Test\Container\Mock\InjectionContainerMock;
use Rela589n\RuntimeInjection\Test\Util\ContainerTearDownTrait;

use function inject;

/** @covers inject */
final class InjectTest extends TestCase
{
    use ContainerTearDownTrait;

    /** @var InjectionEntryPointContainer */
    private $container;

    /** @var InjectionContainerMock */
    private $containerImplementation;

    protected function setUp(): void
    {
        $this->container = InjectionEntryPointContainer::getSelf();
        $this->containerImplementation = new InjectionContainerMock();
        $this->container->wrapContainer($this->containerImplementation);
    }

    public function testUsesEntryPointForInjection(): void
    {
        $object = new stdClass();

        inject($object);

        self::assertSame($object, $this->containerImplementation->lastInject);
    }
}
