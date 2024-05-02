<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\DataTransfer\Example;

use WebServCo\JSONAPI\Contract\Document\MetaInterface;

final readonly class ExampleDocumentMeta implements MetaInterface
{
    public function __construct(public ?string $route, public string $version)
    {
    }
}
