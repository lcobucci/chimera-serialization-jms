<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Unit;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Lcobucci\Chimera\Serialization\Jms\RequestDataInjector;

final class RequestDataInjectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
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
     */
    public function injectDataShouldAddGeneratedIdToData(): void
    {
        $event = $this->createEvent(['foo' => 'bar'], null, null, '1234');

        $this->processListener($event);

        self::assertSame(['_id' => '1234', 'foo' => 'bar'], $event->getData());
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\RequestDataInjector
     */
    public function injectDataShouldMergeThingsWithCorrectPrecedenceData(): void
    {
        $event = $this->createEvent(
            ['_id' => 'nope', 'foo' => 'yeap'],
            ['_id' => 'nope', 'foo' => 'nope', 'bar' => 'yeap'],
            ['_id' => 'nope', 'foo' => 'nope', 'bar' => 'nope', 'baz' => 'yeap'],
            '1234'
        );

        $this->processListener($event);

        self::assertSame(['_id' => '1234', 'foo' => 'yeap', 'bar' => 'yeap', 'baz' => 'yeap'], $event->getData());
    }

    private function createEvent(
        array $data,
        ?array $routeParams = null,
        ?array $queryParams = null,
        ?string $generatedId = null
    ): PreDeserializeEvent {
        $context = DeserializationContext::create();
        $context->increaseDepth();

        if ($routeParams) {
            $context->setAttribute('chimera.route_params', $routeParams);
        }

        if ($queryParams) {
            $context->setAttribute('chimera.query_params', $queryParams);
        }

        if ($generatedId) {
            $context->setAttribute('chimera.generated_id', $generatedId);
        }

        return new PreDeserializeEvent($context, $data, ['name' => Events::PRE_DESERIALIZE, []]);
    }

    private function processListener(PreDeserializeEvent $event): void
    {
        $injector = new RequestDataInjector();
        $injector->injectData($event);
    }
}
