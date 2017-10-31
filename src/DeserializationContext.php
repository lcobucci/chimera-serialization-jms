<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms;

use JMS\Serializer\DeserializationContext as BaseContext;
use Lcobucci\Chimera\Routing\Attributes;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

final class DeserializationContext extends BaseContext
{
    public const ATTR_ROUTER_PARAMS = 'chimera.route_params';
    public const ATTR_QUERY_PARAMS  = 'chimera.query_params';
    public const ATTR_GENERATED_ID  = 'chimera.generated_id';

    /**
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct();

        $this->request = $request;
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        return self::appendRequestAttributes(new self($request), $request);
    }

    private static function appendRequestAttributes(
        self $context,
        ServerRequestInterface $request
    ): DeserializationContext {
        $context->setAttribute(self::ATTR_ROUTER_PARAMS, self::extractRouteParams($request))
                ->setAttribute(self::ATTR_QUERY_PARAMS, $request->getQueryParams());

        $generatedId = $request->getAttribute(Attributes::GENERATED_ID);

        if ($generatedId !== null) {
            $context->setAttribute(self::ATTR_GENERATED_ID, $generatedId);
        }

        return $context;
    }

    private static function extractRouteParams(ServerRequestInterface $request): array
    {
        /** @var RouteResult|null $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);

        if ($routeResult === null) {
            return [];
        }

        return $routeResult->getMatchedParams();
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
