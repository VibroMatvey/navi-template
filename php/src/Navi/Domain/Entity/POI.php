<?php

namespace App\Navi\Domain\Entity;

use App\Shared\Domain\Traits\TimestampTrait;
use App\Shared\Domain\Traits\UlidTrait;

class POI
{
    use UlidTrait, TimestampTrait;

    private string $title;
    private ?Map $map;
    private ?array $naviData;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return POI
     */
    public function setTitle(string $title): POI
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Map|null
     */
    public function getMap(): ?Map
    {
        return $this->map;
    }

    /**
     * @param Map|null $map
     * @return POI
     */
    public function setMap(?Map $map): POI
    {
        $this->map = $map;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getNaviData(): ?array
    {
        return $this->naviData;
    }

    /**
     * @param array|null $naviData
     * @return POI
     */
    public function setNaviData(?array $naviData): POI
    {
        $this->naviData = $naviData;
        return $this;
    }
}