<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms\Tests\Functional;

use JMS\Serializer\Annotation as Serializer;

final class LovelyDto
{
    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $name;

    public function __construct(string $id, string $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}
