<?php

namespace App\Dto;

final class MapObjectDto
{
    public ?int $node = null;
    public ?int $area = null;

    /**
     * @return int
     */
    public function getNode(): int
    {
        return $this->node;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }
}