<?php

declare(strict_types=1);

namespace WebServCo\JSONAPI\View;

use WebServCo\JSONAPI\Contract\Document\JSONAPIInterface;
use WebServCo\JSONAPI\Contract\Document\MetaInterface;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\AbstractView;

final class CollectionView extends AbstractView implements ViewInterface
{
    /**
     * @param array<int,\WebServCo\JSONAPI\Contract\Data\ItemInterface>|null $data
     * @param array<int,\WebServCo\JSONAPI\Contract\Errors\ErrorInterface>|null $errors
     */
    public function __construct(
        // jsonapi is optional
        public readonly ?JSONAPIInterface $jsonapi,
        // data and errors must no co-exist
        public readonly ?array $data,
        // data and errors must no co-exist
        public readonly ?array $errors,
        // meta is optional
        public readonly ?MetaInterface $meta,
    ) {
    }
}
