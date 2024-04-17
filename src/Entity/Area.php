<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Area\AreaCreateController;
use App\Controller\Area\AreaUpdateController;
use App\Dto\AreaDto;
use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            controller: AreaCreateController::class,
            input: AreaDto::class
        ),
        new Patch(
            controller: AreaUpdateController::class,
            input: AreaDto::class
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['area:read']],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['area:read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\ManyToMany(targetEntity: Point::class, inversedBy: 'areas')]
    #[Groups(['area:read'])]
    private Collection $points;

    #[ORM\ManyToOne(inversedBy: 'areas')]
    #[Groups(['area:read'])]
    private ?Floor $floor = null;

    /**
     * @var Collection<int, MapObject>
     */
    #[ORM\ManyToMany(targetEntity: MapObject::class, mappedBy: 'areas')]
    private Collection $mapObjects;

    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->mapObjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Point>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(Point $point): static
    {
        if (!$this->points->contains($point)) {
            $this->points->add($point);
        }

        return $this;
    }

    /**
     * @param Collection $points
     */
    public function setPoints(Collection $points): void
    {
        $this->points = $points;
    }

    public function removePoint(Point $point): static
    {
        $this->points->removeElement($point);

        return $this;
    }

    public function getFloor(): ?Floor
    {
        return $this->floor;
    }

    public function setFloor(?Floor $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * @return Collection<int, MapObject>
     */
    public function getMapObjects(): Collection
    {
        return $this->mapObjects;
    }

    public function addMapObject(MapObject $mapObject): static
    {
        if (!$this->mapObjects->contains($mapObject)) {
            $this->mapObjects->add($mapObject);
            $mapObject->addArea($this);
        }

        return $this;
    }

    public function removeMapObject(MapObject $mapObject): static
    {
        if ($this->mapObjects->removeElement($mapObject)) {
            $mapObject->removeArea($this);
        }

        return $this;
    }
}
