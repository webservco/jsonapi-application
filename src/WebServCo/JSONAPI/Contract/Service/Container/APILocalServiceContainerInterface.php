<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Contract\Service\Container;

use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;

/**
 * A default Local service container for API endpoints.
 *
 * Can be used directly if no special dependencies are required.
 */
interface APILocalServiceContainerInterface extends LocalDependencyContainerInterface
{
    public function getJsonApiServiceContainer(): APIJSONAPIServiceContainerInterface;
}
