# webservco/jsonapi-application

A PHP component/library. Please see [webservco/sandbox](https://webservco.github.io/sandbox/) for an example project using these types of components.

Request validation, document views, error objects and containers for JSON:API endpoints.

- Related: `webservco/jsonapi` (interfaces), `webservco/form` (the request handler works like a form), `webservco/http-request-handler` (`AbstractApiDynamicRequestHandler` for `/api/v1/...` routes), `webservco/jwt` (authentication)

---

## Installation

```shell
composer require webservco/jsonapi-application
```

---

## Usage

This is a shortened version of the `APIController` in the `webservco/sandbox` project. The controller's local container implements `APILocalServiceContainerInterface`.

```php
use Fig\Http\Message\RequestMethodInterface;
use WebServCo\JSONAPI\DataTransfer\Document\JSONAPI;
use WebServCo\JSONAPI\View\ItemView;

// \WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface
$jsonApiHandler = $this->getLocalDependencyContainer()->getJsonApiServiceContainer()
    ->getDefaultHandlerFactory()->createHandler([RequestMethodInterface::METHOD_GET]);

$jsonApiHandler->handleRequest($request);

if (!$jsonApiHandler->isSent() || !$jsonApiHandler->isValid()) {
    // Build an ItemView with $jsonApiHandler->getErrors() turned into DefaultError objects.
}

$viewContainer = $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
    new ItemView(new JSONAPI('1.1'), $itemData, null, $documentMeta),
    // Template used if the client asked for HTML.
    'api/default',
);

return $this->createResponse($request, $viewContainer);
```

The module's request handler should offer `JSONAPIRendererInterface` and `JSONRendererInterface` as renderers.

---

## Contracts

| Interface | Implementation | Purpose |
| --- | --- | --- |
| `Contract\Service\JSONAPIHandlerInterface` | `Service\Handler\JSONAPIItemHandler` | Checks method, content type and version, then fills form fields from `data/attributes`. |
| `Contract\Factory\Handler\JSONAPIHandlerFactoryInterface` | `Factory\Handler\JSONAPIDefaultHandlerFactory` | Creates a handler for a list of methods (no fields). |
| `Contract\Service\JSONAPIRequestServiceInterface` (`webservco/jsonapi`) | `Service\JSONAPIRequestService` | Content type check, version check and JSON body decoding. |
| `Contract\Service\Container\APILocalServiceContainerInterface` | `Container\APILocalServiceContainer` | Local container for API controllers. |
| `Contract\Service\Container\APIJSONAPIServiceContainerInterface` | `Container\APIJSONAPIServiceContainer` | Handler factory and request service. |
| `Contract\Errors\ErrorInterface` (`webservco/jsonapi`) | `DataTransfer\Errors\DefaultError` | `status`, `title`, `detail`. |
| `Contract\ViewInterface` (`webservco/view`) | `View\ItemView`, `View\CollectionView` | Top-level document with `jsonapi`, `data` or `errors`, and `meta`. |

Namespaces are relative to `WebServCo\JSONAPI\`. `DataTransfer\Example\*` are sample DTOs used by the sandbox project.

---

## Notes

For methods that can have a body, the request must have `Content-Type: application/vnd.api+json` and `"jsonapi": {"version": "1.1"}`. Otherwise the handler adds an error with status 400. A `GET` request skips these checks.

A document has either `data` or `errors`. Pass `null` for the other one, and for `jsonapi` and `meta` when you don't need them. They are still encoded, as `null`.

---

## Development

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
# Run all validations.
ddev c c
# Run unit tests.
ddev c test:dox
```

---

## License

[MIT](LICENSE.md)
