<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Container;

use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Http\Service\Message\Request\RequestBodyService;
use WebServCo\Http\Service\Message\Request\RequestHeaderService;
use WebServCo\JSONAPI\Contract\Factory\Handler\JSONAPIHandlerFactoryInterface;
use WebServCo\JSONAPI\Contract\Service\Container\JSONAPIServiceContainerInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;
use WebServCo\JSONAPI\Factory\Handler\JSONAPIDefaultHandlerFactory;
use WebServCo\JSONAPI\Service\JSONAPIRequestService;

/**
 * An abstract JSONAPIServiceContainerInterface implementation.
 */
abstract class AbstractJSONAPIServiceContainer implements JSONAPIServiceContainerInterface
{
    private ?JSONAPIHandlerFactoryInterface $defaultItemHandlerFactory = null;

    private ?JSONAPIRequestServiceInterface $jsonApiRequestService = null;

    public function __construct(protected DataExtractionContainerInterface $dataExtractionContainer)
    {
    }

    public function getDefaultHandlerFactory(): JSONAPIHandlerFactoryInterface
    {
        if ($this->defaultItemHandlerFactory === null) {
            $this->defaultItemHandlerFactory = new JSONAPIDefaultHandlerFactory(
                $this->dataExtractionContainer,
                $this->getJsonApiRequestService(),
            );
        }

        return $this->defaultItemHandlerFactory;
    }

    public function getJsonApiRequestService(): JSONAPIRequestServiceInterface
    {
        if ($this->jsonApiRequestService === null) {
            $this->jsonApiRequestService = new JSONAPIRequestService(
                $this->dataExtractionContainer,
                new RequestBodyService(),
                new RequestHeaderService(),
            );
        }

        return $this->jsonApiRequestService;
    }
}
