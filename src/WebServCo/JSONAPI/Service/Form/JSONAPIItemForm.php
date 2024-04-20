<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\Service\Form;

use Fig\Http\Message\RequestMethodInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Data\Container\Extraction\DataExtractionContainer;
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
        private DataExtractionContainer $dataExtractionContainer,
        private JSONAPIRequestServiceInterface $requestService,
        array $fields,
        array $filters,
        array $validators,
    ) {
        parent::__construct($fields, $filters, $validators);
    }

    public function handleRequest(ServerRequestInterface $request): bool
    {
        // Check content type.
        if (!$this->requestService->contentTypeMatches($request)) {
            // Content type doesn't match, stop processing.
            return false;
        }

        // Check request method.
        $acceptableMethods = [RequestMethodInterface::METHOD_POST, RequestMethodInterface::METHOD_PUT];
        if (!in_array($request->getMethod(), $acceptableMethods, true)) {
            // Request method doesn't match, stop processing.
            return false;
        }

        $requestBodyAsArray = $this->requestService->getRequestBodyAsArray($request);

        // Check version
        $this->requestService->validateVersion($requestBodyAsArray, 1.1);

        // Request method matches, set flag.
        $this->isSent = true;

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
