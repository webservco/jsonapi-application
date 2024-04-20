<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\DataTransfer\Errors;

use WebServCo\JSONAPI\Contract\Errors\ErrorInterface;

final readonly class DefaultError implements ErrorInterface
{
    public function __construct(
        // Not a mistake, status should be a string: https://jsonapi.org/format/#errors
        public string $status,
        public string $title,
        public string $detail,
    ) {
    }
}
