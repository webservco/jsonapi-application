<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Container;

use WebServCo\JSONAPI\Contract\Service\Container\APIJSONAPIServiceContainerInterface;

/**
 * A default JSONAPI service container implementation for API endpoints.
 *
 * Can be used directly if no special dependencies are required.
 */
final class APIJSONAPIServiceContainer extends AbstractJSONAPIServiceContainer implements
    APIJSONAPIServiceContainerInterface
{
}
