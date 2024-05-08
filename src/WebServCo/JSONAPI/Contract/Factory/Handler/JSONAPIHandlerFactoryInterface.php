<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Contract\Factory\Handler;

use WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface;

/**
 * A JSONAPI Handler Factory Interface.
 */
interface JSONAPIHandlerFactoryInterface
{
    /**
     * @param array<int,string> $acceptableRequestMethods
     */
    public function createHandler(array $acceptableRequestMethods): JSONAPIHandlerInterface;
}
