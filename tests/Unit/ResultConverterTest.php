<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Unit;

use JMS\Serializer\SerializerInterface;
use Lcobucci\Chimera\Serialization\Jms\ResultConverter;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

final class ResultConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\ResultConverter
     */
    public function convertShouldUseTheSerializerAndReturnA200Response(): void
    {
        $result = new stdClass();

        $serialiser = $this->createMock(SerializerInterface::class);

        $serialiser->expects($this->once())
                   ->method('serialize')
                   ->with($result, 'json')
                   ->willReturn('{}');

        $converter = new ResultConverter($serialiser);
        $response  = $converter->convert($this->createMock(ServerRequestInterface::class), [], $result);

        self::assertSame('{}', (string) $response->getBody());
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-Type'));
    }
    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\ResultConverter
     */
    public function convertShouldAddGivenHeadersToTheResponse(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);

        $serialiser->method('serialize')
                   ->willReturn('{}');

        $converter = new ResultConverter($serialiser);

        $response = $converter->convert(
            $this->createMock(ServerRequestInterface::class),
            ['Location' => '/1234'],
            new stdClass()
        );

        self::assertSame('/1234', $response->getHeaderLine('Location'));
    }
}
