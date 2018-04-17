<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\MessageCreator\JmsSerializer\Tests\Unit;

use Lcobucci\Chimera\Input;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Lcobucci\Chimera\MessageCreator\JmsSerializer\DeserializationContext
 */
final class DeserializationContextTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getInput()
     */
    public function getInputShouldReturnTheConfiguredObject(): void
    {
        $input   = $this->createMock(Input::class);
        $context = new DeserializationContext($input);

        self::assertSame($input, $context->getInput());
    }
}
