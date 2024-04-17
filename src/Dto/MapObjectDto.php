<?php

namespace App\Dto;

final class MapObjectDto
{
    public ?array $nodes = [];
    public ?array $areas = [];

    /**
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return array
     */
    public function getAreas(): array
    {
        return $this->areas;
    }
}