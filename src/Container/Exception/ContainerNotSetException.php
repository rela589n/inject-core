<?php

declare(strict_types=1);

namespace Rela589n\RuntimeInjection\Container\Exception;

use RuntimeException;

use function get_class;

final class ContainerNotSetException extends RuntimeException
{
    /** @var object */
    private $injectable;

    public function __construct(object $injectable)
    {
        parent::__construct('Injection container was never set. Not able to inject dependencies for '.get_class($injectable));

        $this->injectable = $injectable;
    }

    public function getInjectable(): object
    {
        return $this->injectable;
    }
}
