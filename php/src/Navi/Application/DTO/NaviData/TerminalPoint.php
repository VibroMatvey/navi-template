<?php

namespace App\Navi\Application\DTO\NaviData;

use ApiPlatform\Metadata\ApiProperty;

final readonly class TerminalPoint
{
    public function __construct(
        #[ApiProperty(schema: [
            'type' => 'object',
            'properties' => [
                'x' => ['type' => 'integer'],
                'y' => ['type' => 'integer'],
            ]
        ])]
        public array $cords,
        public array $personCords,
        public int   $terminalId,
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
            $data['personCords'],
            $data['terminalId'],
        );
    }
}