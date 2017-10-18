<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Lcobucci\Chimera\Serialization\Jms\ResultConverter;
use Lcobucci\Chimera\Serialization\Jms\Tests\RequestCreation;

final class JsonMessageSerializationTest extends \PHPUnit\Framework\TestCase
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
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * @test
     *
     * @covers \Lcobucci\Chimera\Serialization\Jms\ResultConverter
     */
    public function dataShouldBeProperlySerialized(): void
    {
        $dto = new LovelyDto('1234', 'Test');

        $converter = new ResultConverter($this->serializer);
        $response  = $converter->convert($this->createRequest(), [], $dto);

        self::assertSame('{"id":"1234","name":"Test"}', (string) $response->getBody());
    }
}
