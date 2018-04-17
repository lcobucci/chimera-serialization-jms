<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\MessageCreator\JmsSerializer;

use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Lcobucci\Chimera\IdentifierGenerator;
use Lcobucci\Chimera\Input;

final class InputDataInjector
{
    public const GENERATED_ID = '_input.id';

    public function injectData(PreDeserializeEvent $event): void
    {
        $context = $event->getContext();

        if (! $context instanceof DeserializationContext || $context->getDepth() !== 1) {
            return;
        }

        $event->setData(
            $this->mergeData((array) $event->getData(), $context->getInput())
        );
    }

    /**
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    private function mergeData(array $data, Input $input): array
    {
        $generatedId = $input->getAttribute(IdentifierGenerator::class);

        if ($generatedId !== null) {
            $data[self::GENERATED_ID] = $generatedId;
        }

        return $data;
    }
}
