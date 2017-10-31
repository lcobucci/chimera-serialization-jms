<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Unit;

use JMS\Serializer\SerializerInterface;
use Lcobucci\Chimera\Serialization\Jms\DeserializationContext;
use Lcobucci\Chimera\Serialization\Jms\MessageCreator;
use Lcobucci\Chimera\Serialization\Jms\Tests\RequestCreation;
use stdClass;

final class MessageCreatorTest extends \PHPUnit\Framework\TestCase
{
    use RequestCreation;

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     * @uses \Lcobucci\Chimera\Serialization\Jms\DeserializationContext
     */
    public function createShouldCreateACustomisedContextAndReturnTheDeserializedObject(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);
        $request    = $this->createRequest('{"test":true}');
        $object     = new stdClass();

        $context = (new DeserializationContext($request))->setAttribute('chimera.route_params', [])
                                                         ->setAttribute('chimera.query_params', []);

        $serialiser->expects($this->once())
                   ->method('deserialize')
                   ->with('{"test":true}', stdClass::class, 'json', $this->equalTo($context))
                   ->willReturn($object);

        $creator = new MessageCreator($serialiser);

        self::assertSame(
            $object,
            $creator->create(stdClass::class, $request)
        );
    }
}
