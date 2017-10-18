<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests;

use Lcobucci\Chimera\Routing\Attributes;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;

trait RequestCreation
{
    private function createRequest(
        ?string $body = null,
        ?array $routeParams = null,
        ?array $queryParams = null,
        ?string $generatedId = null
    ): ServerRequest {
        $request = new ServerRequest();

        if ($body) {
            $content = new Stream('php://temp', 'wb+');
            $content->write($body);
            $content->rewind();

            $request = $request->withBody($content)
                               ->withHeader('Content-Type', 'application/json');
        }

        if ($routeParams) {
            $middleware = function () {
            };

            $request = $request->withAttribute(
                RouteResult::class,
                RouteResult::fromRoute(new Route('/test', $middleware), $routeParams)
            );
        }

        if ($queryParams) {
            $request = $request->withQueryParams($queryParams);
        }

        if ($generatedId) {
            $request = $request->withAttribute(Attributes::GENERATED_ID, $generatedId);
        }

        return $request;
    }
}
