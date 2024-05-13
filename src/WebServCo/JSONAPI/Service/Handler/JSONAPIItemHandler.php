<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Service\Handler;

use Error;
use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Form\Service\AbstractForm;
use WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;

use function in_array;
use function sprintf;

/**
 * A JSONAPI request item handler.
 *
 * Makes use of form system to take advantage of validation/filtering.
 */
final class JSONAPIItemHandler extends AbstractForm implements JSONAPIHandlerInterface
{
    private const array VALID_REQUEST_METHODS = [
        RequestMethodInterface::METHOD_DELETE,
        RequestMethodInterface::METHOD_GET,
        RequestMethodInterface::METHOD_POST,
        RequestMethodInterface::METHOD_PUT,
    ];

    /**
     * @param array<int,string> $acceptableRequestMethods
     * @param array<int,\WebServCo\Form\Contract\FormFieldInterface> $fields
     * @param array<int,\WebServCo\Form\Contract\FormFilterInterface> $filters
     * @param array<int,\WebServCo\Form\Contract\FormValidatorInterface> $validators
     */
    public function __construct(
        private readonly array $acceptableRequestMethods,
        private readonly DataExtractionContainerInterface $dataExtractionContainer,
        private readonly JSONAPIRequestServiceInterface $requestService,
        array $fields,
        array $filters,
        array $validators,
    ) {
        parent::__construct($fields, $filters, $validators);

        foreach ($this->acceptableRequestMethods as $acceptableRequestMethod) {
            if (!in_array($acceptableRequestMethod, self::VALID_REQUEST_METHODS, true)) {
                throw new UnexpectedValueException('Unsupported request method.');
            }
        }
    }

    public function handleRequest(ServerRequestInterface $request): bool
    {
        // Check request method.
        if (!in_array($request->getMethod(), $this->acceptableRequestMethods, true)) {
            $this->addError(new Error('Request method does not match', StatusCodeInterface::STATUS_BAD_REQUEST));

            return false;
        }

        // Request method matches, set flag.
        $this->setSent();

        // Check content type.
        if (!$this->requestService->validateContentType($request)) {
            $this->addError(new Error('Content type does not match.', StatusCodeInterface::STATUS_BAD_REQUEST));

            return false;
        }

        $requestBodyAsArray = $this->requestService->getRequestBodyAsArray($request);

        // Check version
        if (!$this->handleVersionMatchCheck($requestBodyAsArray)) {
            // Error messages already handled.
            return false;
        }

        return $this->handleFormProcessing($requestBodyAsArray);
    }

    /**
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $requestBodyAsArray
     */
    private function handleFormProcessing(array $requestBodyAsArray): bool
    {
        if ($requestBodyAsArray === []) {
            /**
             * Request body is an empty array if request should not have a body.
             *
             * @see JSONAPIRequestService.getRequestBodyAsArray
             */
            return true;
        }

        /**
         * Start from local fields and iterate,
         * because id is stored in the actual formField (string key),
         * it is not the array key, which is an integer.
         *
         * This also avoids having to check fields existence locally,
         * it simply only process stuff that we need.
         */
        foreach ($this->getFields() as $formField) {
            try {
                $formField->setValue(
                    $this->dataExtractionContainer->getLooseArrayDataExtractionService()->getString(
                        $requestBodyAsArray,
                        sprintf('data/attributes/%s', $formField->getId()),
                    ),
                );
            } catch (OutOfBoundsException $e) {
                $this->addFormFieldErrorMessage(
                    new Error($e->getMessage(), StatusCodeInterface::STATUS_BAD_REQUEST),
                    $formField,
                );
            }
        }

        // Filter and validate each field.
        return $this->processForm();
    }

    /**
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $requestBodyAsArray
     */
    private function handleVersionMatchCheck(array $requestBodyAsArray): bool
    {
        if ($requestBodyAsArray === []) {
            /**
             * Request body is an empty array if request should not have a body.
             *
             * @see JSONAPIRequestService.getRequestBodyAsArray
             */
            return true;
        }

        try {
            if (!$this->requestService->versionMatches($requestBodyAsArray, 1.1)) {
                $this->addError(new Error('JSONAPI version does not match.', StatusCodeInterface::STATUS_BAD_REQUEST));

                return false;
            }
        } catch (OutOfBoundsException $exception) {
            $this->addError(new Error($exception->getMessage(), StatusCodeInterface::STATUS_BAD_REQUEST));

            return false;
        }

        return true;
    }
}
