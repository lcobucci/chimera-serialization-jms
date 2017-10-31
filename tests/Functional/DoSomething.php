<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Functional;

use JMS\Serializer\Annotation as Serializer;
use Lcobucci\Chimera\Serialization\Jms\RequestDataInjector;

final class DoSomething
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName(RequestDataInjector::GENERATED_ID)
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
