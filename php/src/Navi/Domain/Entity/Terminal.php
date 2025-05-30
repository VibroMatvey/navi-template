<?php

namespace App\Navi\Domain\Entity;

use App\Shared\Domain\Traits\TimestampTrait;
use App\Shared\Domain\Traits\UlidTrait;

class Terminal
{
    use UlidTrait, TimestampTrait;

    private string $name;
    private ?Map $map;
    private ?array $naviData;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Terminal
     */
    public function setName(string $name): Terminal
    {
        $this->name = $name;
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
     * @return Terminal
     */
    public function setMap(?Map $map): Terminal
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
     * @return Terminal
     */
    public function setNaviData(?array $naviData): Terminal
    {
        $this->naviData = $naviData;
        return $this;
    }
}