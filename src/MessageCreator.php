<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms;

use JMS\Serializer\SerializerInterface;
use Lcobucci\Chimera\MessageCreator as MessageCreatorInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MessageCreator implements MessageCreatorInterface
{
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
            DeserializationContext::fromRequest($request)
        );
    }
}
