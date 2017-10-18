<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Unit;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
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
     */
    public function createShouldCreateACustomisedContextAndReturnTheDeserializedObject(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);
        $object     = new stdClass();

        $context = DeserializationContext::create()->setAttribute('chimera.route_params', [])
                                                   ->setAttribute('chimera.query_params', []);

        $serialiser->expects($this->once())
                   ->method('deserialize')
                   ->with('{"test":true}', stdClass::class, 'json', $this->equalTo($context))
                   ->willReturn($object);

        $creator = new MessageCreator($serialiser);

        self::assertSame(
            $object,
            $creator->create(stdClass::class, $this->createRequest('{"test":true}'))
        );
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     */
    public function createShouldBeAbleToProcessRequestsWithEmptyBody(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);
        $object     = new stdClass();

        $context = DeserializationContext::create()->setAttribute('chimera.route_params', [])
                                                   ->setAttribute('chimera.query_params', []);

        $serialiser->expects($this->once())
                   ->method('deserialize')
                   ->with('{}', stdClass::class, 'json', $this->equalTo($context))
                   ->willReturn($object);

        $creator = new MessageCreator($serialiser);

        self::assertSame(
            $object,
            $creator->create(stdClass::class, $this->createRequest())
        );
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     */
    public function createShouldForwardRouteParamsProperly(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);
        $object     = new stdClass();

        $context = DeserializationContext::create()->setAttribute('chimera.route_params', ['foo' => 'bar'])
                                                   ->setAttribute('chimera.query_params', []);

        $serialiser->expects($this->once())
                   ->method('deserialize')
                   ->with('{}', stdClass::class, 'json', $this->equalTo($context))
                   ->willReturn($object);

        $creator = new MessageCreator($serialiser);

        self::assertSame(
            $object,
            $creator->create(stdClass::class, $this->createRequest(null, ['foo' => 'bar']))
        );
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     */
    public function createShouldForwardQueryParamsProperly(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);
        $object     = new stdClass();

        $context = DeserializationContext::create()->setAttribute('chimera.route_params', [])
                                                   ->setAttribute('chimera.query_params', ['foo' => 'bar']);

        $serialiser->expects($this->once())
                   ->method('deserialize')
                   ->with('{}', stdClass::class, 'json', $this->equalTo($context))
                   ->willReturn($object);

        $creator = new MessageCreator($serialiser);

        self::assertSame(
            $object,
            $creator->create(stdClass::class, $this->createRequest(null, null, ['foo' => 'bar']))
        );
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     */
    public function createShouldForwardGeneratedIdProperly(): void
    {
        $serialiser = $this->createMock(SerializerInterface::class);
        $object     = new stdClass();

        $context = DeserializationContext::create()->setAttribute('chimera.route_params', [])
                                                   ->setAttribute('chimera.query_params', [])
                                                   ->setAttribute('chimera.generated_id', '1234');

        $serialiser->expects($this->once())
                   ->method('deserialize')
                   ->with('{}', stdClass::class, 'json', $this->equalTo($context))
                   ->willReturn($object);

        $creator = new MessageCreator($serialiser);

        self::assertSame(
            $object,
            $creator->create(stdClass::class, $this->createRequest(null, null, null, '1234'))
        );
    }
}
