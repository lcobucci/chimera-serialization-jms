<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Lcobucci\Chimera\MessageCreator as MessageCreatorInterface;
use Lcobucci\Chimera\Routing\Attributes;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouteResult;

final class MessageCreator implements MessageCreatorInterface
{
    public const ATTR_ROUTER_PARAMS = 'chimera.route_params';
    public const ATTR_QUERY_PARAMS  = 'chimera.query_params';
    public const ATTR_GENERATED_ID  = 'chimera.generated_id';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function create(string $message, ServerRequestInterface $request)
    {
        $body = (string) $request->getBody();

        return $this->serializer->deserialize(
            $body === '' ? '{}' : $body,
            $message,
            'json',
            $this->createContext($request)
        );
    }

    private function createContext(ServerRequestInterface $request): DeserializationContext
    {
        $routeParams = $this->extractRouteParams($request);
        $generatedId = $request->getAttribute(Attributes::GENERATED_ID);

        $context = DeserializationContext::create()->setAttribute(self::ATTR_ROUTER_PARAMS, $routeParams)
                                                   ->setAttribute(self::ATTR_QUERY_PARAMS, $request->getQueryParams());

        if ($generatedId !== null) {
            $context->setAttribute(self::ATTR_GENERATED_ID, $generatedId);
        }

        return $context;
    }

    private function extractRouteParams(ServerRequestInterface $request): array
    {
        /** @var RouteResult|null $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);

        if ($routeResult === null) {
            return [];
        }

        return $routeResult->getMatchedParams();
    }
}
