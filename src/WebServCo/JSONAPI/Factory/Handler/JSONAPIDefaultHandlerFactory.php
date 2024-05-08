<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Factory\Handler;

use Fig\Http\Message\RequestMethodInterface;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\JSONAPI\Contract\Factory\Handler\JSONAPIHandlerFactoryInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;
use WebServCo\JSONAPI\Service\Handler\JSONAPIItemHandler;

/**
 * Creates a default JSONAPI GET handler.
 *
 * Use case: take advantage of form validation to handle JSONAPI request errors.
 */
final class JSONAPIDefaultHandlerFactory implements JSONAPIHandlerFactoryInterface
{
    public function __construct(
        private DataExtractionContainerInterface $dataExtractionContainer,
        private JSONAPIRequestServiceInterface $jsonApiRequestService,
    ) {
    }

    /**
     * @param array<int,string> $acceptableRequestMethods
     */
    public function createHandler(
        array $acceptableRequestMethods = [RequestMethodInterface::METHOD_GET],
    ): JSONAPIHandlerInterface {
        return new JSONAPIItemHandler(
            $acceptableRequestMethods,
            $this->dataExtractionContainer,
            $this->jsonApiRequestService,
            // no fields
            [],
            // no filters
            [],
            // no validators
            [],
        );
    }
}
