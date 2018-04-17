<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\MessageCreator\JmsSerializer\Tests\Functional;

use JMS\Serializer\Annotation as Serializer;
use Lcobucci\Chimera\MessageCreator\JmsSerializer\InputDataInjector;

final class DoSomething
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName(InputDataInjector::GENERATED_ID)
     *
     * @var string
     */
    public $id;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    public $foo;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    public $bar;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    public $baz;
}
