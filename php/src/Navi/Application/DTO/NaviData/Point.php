<?php

namespace App\Navi\Application\DTO\NaviData;

use ApiPlatform\Metadata\ApiProperty;

final readonly class Point
{
    public function __construct(
        #[ApiProperty(schema: [
            'type' => 'object',
            'properties' => [
                'x' => ['type' => 'integer'],
                'y' => ['type' => 'integer'],
            ]
        ])]
        public array  $cords,
        public int    $nodeId,
        public int    $pointId,
        public string $type,
    )
    {
    }

    public static function fromArray(?array $data): ?self
    {
        if (!$data) {
            return null;
        }
        return new self(
            $data['cords'],
            $data['nodeId'],
            $data['pointId'],
            $data['type']
        );
    }
}