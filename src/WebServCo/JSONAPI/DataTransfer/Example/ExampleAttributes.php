<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\DataTransfer\Example;

final readonly class ExampleAttributes
{
    public function __construct(public ?string $routePart3, public string $userId)
    {
    }
}
