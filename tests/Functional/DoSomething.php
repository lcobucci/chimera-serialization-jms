<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Functional;

use JMS\Serializer\Annotation as Serializer;

final class DoSomething
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("_id")
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
