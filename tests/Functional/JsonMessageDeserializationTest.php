<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Lcobucci\Chimera\Serialization\Jms\MessageCreator;
use Lcobucci\Chimera\Serialization\Jms\RequestDataInjector;
use Lcobucci\Chimera\Serialization\Jms\Tests\RequestCreation;

final class JsonMessageDeserializationTest extends \PHPUnit\Framework\TestCase
{
    use RequestCreation;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @beforeClass
     */
    public static function registerAutoloader(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @before
     */
    public function createSerializer(): void
    {
        $injector = new RequestDataInjector();

        $addListeners = function (EventDispatcher $dispatcher) use ($injector): void {
            $dispatcher->addListener(Events::PRE_DESERIALIZE, [$injector, 'injectData']);
        };

        $this->serializer = SerializerBuilder::create()->configureListeners($addListeners)
                                                       ->build();
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     */
    public function requestBodyShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage('{"foo":"one","bar":"two","baz":"three"}');

        self::assertNull($message->id);
        self::assertSame('one', $message->foo);
        self::assertSame('two', $message->bar);
        self::assertSame('three', $message->baz);
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     */
    public function routeParamsShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage(null, ['foo' => 'one', 'bar' => 'two', 'baz' => 'three']);

        self::assertNull($message->id);
        self::assertSame('one', $message->foo);
        self::assertSame('two', $message->bar);
        self::assertSame('three', $message->baz);
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     */
    public function queryStringParamsShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage(null, null, ['foo' => 'one', 'bar' => 'two', 'baz' => 'three']);

        self::assertNull($message->id);
        self::assertSame('one', $message->foo);
        self::assertSame('two', $message->bar);
        self::assertSame('three', $message->baz);
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     */
    public function generatedIdShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage(null, null, null, '1234');

        self::assertSame('1234', $message->id);
        self::assertNull($message->foo);
        self::assertNull($message->bar);
        self::assertNull($message->baz);
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\MessageCreator
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     */
    public function requestDataShouldBeUsedOnDeserializationInTheCorrectPrecedence(): void
    {
        $message = $this->createMessage(
            '{"_id":"nope","foo":"yeap"}',
            ['_id' => 'nope', 'foo' => 'nope', 'bar' => 'yeap'],
            ['_id' => 'nope', 'foo' => 'nope', 'bar' => 'nope', 'baz' => 'yeap'],
            'yeap'
        );

        self::assertSame('yeap', $message->id);
        self::assertSame('yeap', $message->foo);
        self::assertSame('yeap', $message->bar);
        self::assertSame('yeap', $message->baz);
    }

    private function createMessage(
        ?string $body = null,
        ?array $routeParams = null,
        ?array $queryParams = null,
        ?string $generatedId = null
    ): DoSomething {
        $creator = new MessageCreator($this->serializer);

        return $creator->create(
            DoSomething::class,
            $this->createRequest($body, $routeParams, $queryParams, $generatedId)
        );
    }
}
