<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\MessageCreator\JmsSerializer\Tests\Unit;

use JMS\Serializer\ArrayTransformerInterface;
use Lcobucci\Chimera\Input;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @coversDefaultClass \Lcobucci\Chimera\MessageCreator\JmsSerializer\ArrayTransformer
 */
final class ArrayTransformerTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct()
     * @covers ::create()
     *
     * @uses \Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext
     */
    public function createShouldReturnANewInstanceOfGivenMessageUsingTheInputData(): void
    {
        $input       = $this->createMock(Input::class);
        $transformer = $this->createMock(ArrayTransformerInterface::class);
        $data        = ['test' => 1];

        $input->method('getData')
              ->willReturn($data);

        $transformer->expects(self::once())
                    ->method('fromArray')
                    ->with($data, stdClass::class, new DeserializationContext($input))
                    ->willReturn((object) $data);

        $creator = new ArrayTransformer($transformer);
        $message = $creator->create(stdClass::class, $input);

        self::assertInstanceOf(stdClass::class, $message);
        self::assertSame(1, $message->test);
    }
}
