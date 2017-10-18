<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms;

use JMS\Serializer\SerializerInterface;
use Lcobucci\Chimera\Routing\Expressive\ResultConverter as ResultConverterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

final class ResultConverter implements ResultConverterInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function convert(ServerRequestInterface $request, array $headers, $result): ResponseInterface
    {
        return new Response(
            $this->createBody($this->serializer->serialize($result, 'json')),
            200,
            $headers + ['Content-Type' => 'application/json']
        );
    }

    private function createBody(string $result): Stream
    {
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($result);
        $stream->rewind();

        return $stream;
    }
}
