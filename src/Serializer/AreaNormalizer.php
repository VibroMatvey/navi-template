<?php

namespace App\Serializer;

use App\Entity\Area;
use App\Entity\Floor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

readonly class AreaNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /* @var Area $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $points = [];

        foreach ($object->getPoints() as $point) {
            $points[] = $point->getId();
        }

        $data['points'] = $points;
        $data['floor_id'] = $object->getFloor()?->getId();
        unset($data['floor']);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Area;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Area::class => true,
        ];
    }
}