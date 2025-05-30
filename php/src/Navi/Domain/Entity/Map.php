<?php

namespace App\Navi\Domain\Entity;

use App\Shared\Domain\Traits\TimestampTrait;
use App\Shared\Domain\Traits\UlidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Map
{
    public const string UPLOAD_DIR = "/uploads/maps/";
    use UlidTrait, TimestampTrait;

    private string $name;
    private string $image;
    private Collection $points;
    private Collection $terminals;

    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->terminals = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    /**
     * @param Collection $points
     * @return Map
     */
    public function setPoints(Collection $points): Map
    {
        $this->points = $points;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Map
     */
    public function setImage(string $image): Map
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Map
     */
    public function setName(string $name): Map
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTerminals(): Collection
    {
        return $this->terminals;
    }

    /**
     * @param Collection $terminals
     * @return Map
     */
    public function setTerminals(Collection $terminals): Map
    {
        $this->terminals = $terminals;
        return $this;
    }
}