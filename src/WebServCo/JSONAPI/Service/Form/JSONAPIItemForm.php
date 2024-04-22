<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Service\Form;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Form\Service\AbstractForm;
use WebServCo\JSONAPI\Contract\Service\JSONAPIRequestServiceInterface;

use function in_array;
use function sprintf;

final class JSONAPIItemForm extends AbstractForm implements FormInterface
{
    /**
     * @param array<int,\WebServCo\Form\Contract\FormFieldInterface> $fields
     * @param array<int,\WebServCo\Form\Contract\FormFilterInterface> $filters
     * @param array<int,\WebServCo\Form\Contract\FormValidatorInterface> $validators
     */
    public function __construct(
        private DataExtractionContainerInterface $dataExtractionContainer,
        private JSONAPIRequestServiceInterface $requestService,
        array $fields,
        array $filters,
        array $validators,
    ) {
        parent::__construct($fields, $filters, $validators);
    }

    public function handleRequest(ServerRequestInterface $request): bool
    {
        // Check request method.
        $acceptableMethods = [RequestMethodInterface::METHOD_POST, RequestMethodInterface::METHOD_PUT];
        if (!in_array($request->getMethod(), $acceptableMethods, true)) {
            $this->addErrorMessage('Request method does not match');

            return false;
        }

        // Request method matches, set flag.
        $this->isSent = true;

        // Check content type.
        if (!$this->requestService->contentTypeMatches($request)) {
            $this->addErrorMessage('Content type does not match.');

            return false;
        }

        $requestBodyAsArray = $this->requestService->getRequestBodyAsArray($request);

        // Check version
        if (!$this->requestService->versionMatches($requestBodyAsArray, 1.1)) {
            $this->addErrorMessage('JSONAPI version does not match.');

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
        /**
         * Start from local fields and iterate,
         * because id is stored in the actual formField (string key),
         * it is not the array key, which is an integer.
         *
         * This also avoids having to check fields existence locally,
         * it simply only process stuff that we need.
         */
        foreach ($this->getFields() as $formField) {
            $formField->setValue(
                $this->dataExtractionContainer->getLooseArrayDataExtractionService()->getString(
                    $requestBodyAsArray,
                    sprintf('data/attributes/%s', $formField->getId()),
                ),
            );
        }

        // Filter and validate each field.
        return $this->processForm();
    }
}
