<?php
declare(strict_types=1);

namespace Lcobucci\Chimera\Serialization\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

final class RequestDataInjector
{
    public function injectData(PreDeserializeEvent $event): void
    {
        $context = $event->getContext();

        if ($context->getDepth() !== 1) {
            return;
        }

        $event->setData($this->mergeData($event->getData(), $context));
    }

    private function mergeData(array $data, Context $context): array
    {
        $attributes = $context->attributes->all();
        $idValue    = $attributes[MessageCreator::ATTR_GENERATED_ID] ?? null;

        $generatedId = $idValue ? ['_id' => $idValue] : [];
        $routeParams = $attributes[MessageCreator::ATTR_ROUTER_PARAMS] ?? [];
        $queryParams = $attributes[MessageCreator::ATTR_QUERY_PARAMS] ?? [];

        return $generatedId + $data + $routeParams + $queryParams;
    }
}
