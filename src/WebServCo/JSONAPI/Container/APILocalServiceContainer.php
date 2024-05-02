<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Container;

use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\JSONAPI\Contract\Service\Container\APIJSONAPIServiceContainerInterface;
use WebServCo\JSONAPI\Contract\Service\Container\APILocalServiceContainerInterface;

/**
 * A default local service container implementation for API endpoints.
 *
 * Can be used directly if no special dependencies are required.
 */
final class APILocalServiceContainer implements APILocalServiceContainerInterface
{
    private ?APIJSONAPIServiceContainerInterface $jsonApiServiceContainer = null;

    public function __construct(private DataExtractionContainerInterface $dataExtractionContainer)
    {
    }

    public function getJsonApiServiceContainer(): APIJSONAPIServiceContainerInterface
    {
        if ($this->jsonApiServiceContainer === null) {
            $this->jsonApiServiceContainer = new APIJSONAPIServiceContainer($this->dataExtractionContainer);
        }

        return $this->jsonApiServiceContainer;
    }
}
