<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Service;

use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\JSONAPI\Contract\Document\JSONAPIInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;

use function array_key_exists;
use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class JSONAPIRequestService implements JSONAPIRequestServiceInterface
{
    public function __construct(private readonly DataExtractionContainerInterface $dataExtractionContainer)
    {
    }

    public function contentTypeMatches(ServerRequestInterface $request): bool
    {
        $contentTypeHeaderValue = $this->getContentTypeHeaderValue($request);

        return $contentTypeHeaderValue === JSONAPIInterface::MEDIA_TYPE;
    }

    public function getContentTypeHeaderValue(ServerRequestInterface $request): string
    {
        $headers = $request->getHeader('Content-Type');
        if (!array_key_exists(0, $headers)) {
            throw new UnexpectedValueException('Missing required header');
        }

        return $headers[0];
    }

    /**
     * @inheritDoc
     */
    public function getRequestBodyAsArray(ServerRequestInterface $request): array
    {
        $requestBody = $request->getBody()->getContents();

        if ($requestBody === '') {
            // Possible situation: the body contents were read elsewhere and the stream was not rewinded.
            throw new UnexpectedValueException('Response body is empty.');
        }

        $array = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);

        // Important! Otherwise, the stream body contents can not be retrieved later.
        $request->getBody()->rewind();

        if (!is_array($array)) {
            throw new UnexpectedValueException('Error decoding JSON data.');
        }

        return $array;
    }

    /**
     * @inheritDoc
     */
    public function validateVersion(array $requestBodyAsArray, float $expectedVersion = 1.1): bool
    {
        $version = $this->dataExtractionContainer->getLooseArrayNonEmptyDataExtractionService()->getNonEmptyFloat(
            $requestBodyAsArray,
            'jsonapi/version',
        );

        if ($version !== $expectedVersion) {
            throw new UnexpectedValueException('Invalid jsonapi.version.');
        }

        return true;
    }
}
