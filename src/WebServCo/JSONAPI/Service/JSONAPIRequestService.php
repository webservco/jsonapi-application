<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Service;

use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Http\Contract\Message\Request\RequestBodyServiceInterface;
use WebServCo\Http\Contract\Message\Request\RequestHeaderServiceInterface;
use WebServCo\JSONAPI\Contract\Document\JSONAPIInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;

use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class JSONAPIRequestService implements JSONAPIRequestServiceInterface
{
    public function __construct(
        private readonly DataExtractionContainerInterface $dataExtractionContainer,
        private readonly RequestBodyServiceInterface $requestBodyService,
        private readonly RequestHeaderServiceInterface $requestHeaderService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getRequestBodyAsArray(ServerRequestInterface $request): array
    {
        $requestBody = $request->getBody()->getContents();

        // Important! Otherwise, the stream body contents can not be retrieved later.
        $request->getBody()->rewind();

        if ($requestBody === '') {
            if (!$this->requestBodyService->canHaveRequestBody($request)) {
                // Correct that there is no request body.
                return [];
            }

            // Possible situation: the body contents were read elsewhere and the stream was not rewinded.
            throw new UnexpectedValueException('Request body is empty.');
        }

        $array = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($array)) {
            throw new UnexpectedValueException('Error decoding JSON data.');
        }

        return $array;
    }

    public function validateContentType(ServerRequestInterface $request): bool
    {
        if (!$this->requestBodyService->canHaveRequestBody($request)) {
            // No request body, nothing to check.
            return true;
        }

        $contentTypeHeaderValue = $this->requestHeaderService->getHeaderValue('Content-Type', $request);

        return $contentTypeHeaderValue === JSONAPIInterface::MEDIA_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function versionMatches(array $requestBodyAsArray, float $expectedVersion = 1.1): bool
    {
        $version = $this->dataExtractionContainer->getLooseArrayNonEmptyDataExtractionService()->getNonEmptyFloat(
            $requestBodyAsArray,
            'jsonapi/version',
        );

        return $version === $expectedVersion;
    }
}
