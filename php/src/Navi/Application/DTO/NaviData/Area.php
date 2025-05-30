<?php

namespace App\Navi\Application\DTO\NaviData;

use ApiPlatform\Metadata\ApiProperty;

final readonly class Area
{
    public function __construct(
        #[ApiProperty(schema: [
            'type' => 'array',
            'items' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'x' => ['type' => 'integer'],
                        'y' => ['type' => 'integer'],
                    ]
                ]
            ]
        ])]
        public array  $cords,
        public int    $nodeId,
        public int    $areaId,
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
            $data['areaId'],
            $data['type'],
        );
    }
}