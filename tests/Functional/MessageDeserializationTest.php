<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\MessageCreator\JmsSerializer\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Lcobucci\Chimera\IdentifierGenerator;
use Lcobucci\Chimera\Input;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\InputDataInjector;
use function assert;

final class MessageDeserializationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @beforeClass
     */
    public static function registerAutoloader(): void
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
    }

    /**
     * @before
     */
    public function createSerializer(): void
    {
        $injector = new InputDataInjector();

        $addListeners = function (EventDispatcher $dispatcher) use ($injector): void {
            $dispatcher->addListener(Events::PRE_DESERIALIZE, [$injector, 'injectData']);
        };

        $this->serializer = SerializerBuilder::create()->configureListeners($addListeners)
                                                       ->build();
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\InputDataInjector
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext
     */
    public function inputDataShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage(['foo' => 'one', 'bar' => 'two', 'baz' => 'three']);

        self::assertNull($message->id);
        self::assertSame('one', $message->foo);
        self::assertSame('two', $message->bar);
        self::assertSame('three', $message->baz);
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\InputDataInjector
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext
     */
    public function generatedIdShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage([], '1234');

        self::assertSame('1234', $message->id);
        self::assertNull($message->foo);
        self::assertNull($message->bar);
        self::assertNull($message->baz);
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\InputDataInjector
     * @covers \Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext
     */
    public function dataAndIdShouldBeUsedOnDeserialization(): void
    {
        $message = $this->createMessage(['foo' => 'one', 'bar' => 'two', 'baz' => 'three'], '1234');

        self::assertSame('1234', $message->id);
        self::assertSame('one', $message->foo);
        self::assertSame('two', $message->bar);
        self::assertSame('three', $message->baz);
    }

    private function createMessage(
        array $data = [],
        ?string $generatedId = null
    ): DoSomething {
        $creator = new ArrayTransformer($this->serializer);
        $message = $creator->create(DoSomething::class, $this->createInput($data, $generatedId));
        assert($message instanceof DoSomething);

        return $message;
    }

    /**
     * @param mixed[] $data
     */
    private function createInput(array $data, ?string $generatedId): Input
    {
        return new class($data, $generatedId) implements Input
        {
            /**
             * @var mixed[]
             */
            private $data;

            /**
             * @var mixed[]
             */
            private $attributes = [];

            public function __construct(array $data, ?string $generatedId = null)
            {
                $this->data = $data;

                if ($generatedId !== null) {
                    $this->attributes[IdentifierGenerator::class] = $generatedId;
                }
            }

            /**
             * {@inheritdoc}
             */
            public function getAttribute(string $name, $default = null)
            {
                return $this->attributes[$name] ?? $default;
            }

            /**
             * {@inheritdoc}
             */
            public function getData(): array
            {
                return $this->data;
            }
        };
    }
}
