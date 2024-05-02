<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Contract\Factory\Handler;

use WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface;

/**
 * A JSONAPI Handler Factory Interface.
 */
interface JSONAPIHandlerFactoryInterface
{
    public function createHandler(): JSONAPIHandlerInterface;
}
