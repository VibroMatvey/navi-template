<?php

namespace App\Dto;

final class NodeDto
{
    public ?PointDto $point = null;

    public ?array $nodes = null;

    public ?array $types = null;

    /**
     * @return array|null
     */
    public function getNodes(): ?array
    {
        return $this->nodes;
    }

    /**
     * @return PointDto|null
     */
    public function getPoint(): ?PointDto
    {
        return $this->point;
    }

    /**
     * @return array|null
     */
    public function getTypes(): ?array
    {
        return $this->types;
    }
}