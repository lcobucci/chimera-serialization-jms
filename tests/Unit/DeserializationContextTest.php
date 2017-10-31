<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Unit;

use Lcobucci\Chimera\Serialization\Jms\DeserializationContext;
use Lcobucci\Chimera\Serialization\Jms\Tests\RequestCreation;

final class DeserializationContextTest extends \PHPUnit\Framework\TestCase
{
    use RequestCreation;

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\DeserializationContext::__construct
     * @covers \Lcobucci\Chimera\Serialization\Jms\DeserializationContext::getRequest
     */
    public function getRequestShouldReturnTheConfiguredRequest(): void
    {
        $request = $this->createRequest();
        $context = new DeserializationContext($request);

        self::assertSame($request, $context->getRequest());
    }

    /**
     * @test
     *
     * @dataProvider requestDataProvider
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\DeserializationContext::fromRequest
     * @covers \Lcobucci\Chimera\Serialization\Jms\DeserializationContext::appendRequestAttributes
     * @covers \Lcobucci\Chimera\Serialization\Jms\DeserializationContext::extractRouteParams
     * @covers \Lcobucci\Chimera\Serialization\Jms\DeserializationContext::__construct
     */
    public function fromRequestShouldConfigureAttributesProperly(
        ?array $routeParams = null,
        ?array $queryParams = null,
        ?string $generatedId = null
    ): void {
        $request = $this->createRequest(null, $routeParams, $queryParams, $generatedId);

        $expected = (new DeserializationContext($request))->setAttribute('chimera.route_params', $routeParams ?? [])
                                                          ->setAttribute('chimera.query_params', $queryParams ?? []);

        if ($generatedId) {
            $expected->setAttribute('chimera.generated_id', $generatedId);
        }

        self::assertEquals($expected, DeserializationContext::fromRequest($request));
    }

    public function requestDataProvider(): array
    {
        return [
            'no data'      => [],
            'route params' => [['foo' => 'bar']],
            'query params' => [null, ['bar' => 'baz']],
            'id'           => [null, null, '1234'],
            'everything'   => [['foo' => 'bar'], ['bar' => 'baz'], '1234'],
        ];
    }
}
