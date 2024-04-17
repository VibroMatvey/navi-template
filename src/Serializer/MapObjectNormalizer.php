<?php

namespace App\Serializer;

use App\Entity\MapObject;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class MapObjectNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var MapObject $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $nodes = [];
        $areas = [];

        foreach ($object->getNodes() as $node) {
            $nodes[] = $node->getId();
        }

        foreach ($object->getAreas() as $area) {
            $areas[] = $area->getId();
        }

        $data['nodes'] = $nodes;
        $data['areas'] = $areas;

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof MapObject;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            MapObject::class => true,
        ];
    }
}