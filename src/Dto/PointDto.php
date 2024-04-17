<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PointDto
{
    #[Assert\NotNull]
    public ?int $x = null;

    #[Assert\NotNull]
    public ?int $y = null;

    #[Assert\NotNull]
    public ?int $floor = null;

    /**
     * @return int|null
     */
    public function getFloor(): ?int
    {
        return $this->floor;
    }

    /**
     * @return int|null
     */
    public function getX(): ?int
    {
        return $this->x;
    }

    /**
     * @return int|null
     */
    public function getY(): ?int
    {
        return $this->y;
    }
}