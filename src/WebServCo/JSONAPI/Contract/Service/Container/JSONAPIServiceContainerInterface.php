<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Contract\Service\Container;

use WebServCo\JSONAPI\Contract\Factory\Handler\JSONAPIHandlerFactoryInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;

/**
 * A JSONAPI Service Container Interface.
 */
interface JSONAPIServiceContainerInterface
{
    /**
     * A factory for a default JSONAPI handler.
     *
     * Use case: no custom JSONAPI handler implementation is needed.
     */
    public function getDefaultHandlerFactory(): JSONAPIHandlerFactoryInterface;

    public function getJsonApiRequestService(): JSONAPIRequestServiceInterface;
}
