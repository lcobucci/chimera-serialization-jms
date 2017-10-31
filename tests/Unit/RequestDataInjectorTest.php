<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Unit;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Lcobucci\Chimera\Serialization\Jms\DeserializationContext;
use Lcobucci\Chimera\Serialization\Jms\RequestDataInjector;
use Lcobucci\Chimera\Serialization\Jms\Tests\RequestCreation;

final class RequestDataInjectorTest extends \PHPUnit\Framework\TestCase
{
    use RequestCreation;

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     * @uses \Lcobucci\Chimera\Serialization\Jms\DeserializationContext
     */
    public function injectDataShouldBeSkippedWhenContextDepthIsNotOne(): void
    {
        $event = $this->createEvent(['foo' => 'bar'], ['bar' => 'baz']);

        /** @var DeserializationContext $context */
        $context = $event->getContext();
        $context->increaseDepth();

        $this->processListener($event);

        self::assertSame(['foo' => 'bar'], $event->getData());
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     * @uses \Lcobucci\Chimera\Serialization\Jms\DeserializationContext
     */
    public function injectDataShouldAddRouteParamsToData(): void
    {
        $event = $this->createEvent(['foo' => 'bar'], ['bar' => 'baz']);

        $this->processListener($event);

        self::assertSame(['foo' => 'bar', 'bar' => 'baz'], $event->getData());
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     * @uses \Lcobucci\Chimera\Serialization\Jms\DeserializationContext
     */
    public function injectDataShouldAddQueryParamsToData(): void
    {
        $event = $this->createEvent(['foo' => 'bar'], null, ['bar' => 'baz']);

        $this->processListener($event);

        self::assertSame(['foo' => 'bar', 'bar' => 'baz'], $event->getData());
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     * @uses \Lcobucci\Chimera\Serialization\Jms\DeserializationContext
     */
    public function injectDataShouldAddGeneratedIdToData(): void
    {
        $event = $this->createEvent(['foo' => 'bar'], null, null, '1234');

        $this->processListener($event);

        self::assertSame(['_request.id' => '1234', 'foo' => 'bar'], $event->getData());
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     * @uses \Lcobucci\Chimera\Serialization\Jms\DeserializationContext
     */
    public function injectDataShouldMergeThingsWithCorrectPrecedenceData(): void
    {
        $event = $this->createEvent(
            ['_request.id' => 'nope', 'foo' => 'yeap'],
            ['_request.id' => 'nope', 'foo' => 'nope', 'bar' => 'yeap'],
            ['_request.id' => 'nope', 'foo' => 'nope', 'bar' => 'nope', 'baz' => 'yeap'],
            '1234'
        );

        $this->processListener($event);

        self::assertSame(
            ['_request.id' => '1234', 'foo' => 'yeap', 'bar' => 'yeap', 'baz' => 'yeap'],
            $event->getData()
        );
    }

    private function createEvent(
        array $data,
        ?array $routeParams = null,
        ?array $queryParams = null,
        ?string $generatedId = null
    ): PreDeserializeEvent {
        $context = DeserializationContext::fromRequest(
            $this->createRequest(null, $routeParams, $queryParams, $generatedId)
        );

        $context->increaseDepth();

        return new PreDeserializeEvent($context, $data, ['name' => Events::PRE_DESERIALIZE, []]);
    }

    private function processListener(PreDeserializeEvent $event): void
    {
        $injector = new RequestDataInjector();
        $injector->injectData($event);
    }
}
