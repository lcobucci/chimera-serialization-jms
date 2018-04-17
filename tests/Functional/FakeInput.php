<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\MessageCreator\JmsSerializer\Tests\Functional;

use Lcobucci\Chimera\IdentifierGenerator;
use Lcobucci\Chimera\Input;

final class FakeInput implements Input
{
    /**
     * @var mixed[]
     */
    private $data;

    /**
     * @var mixed[]
     */
    private $attributes = [];

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data, ?string $generatedId = null)
    {
        $this->data = $data;

        if ($generatedId === null) {
            return;
        }

        $this->attributes[IdentifierGenerator::class] = $generatedId;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }
}
