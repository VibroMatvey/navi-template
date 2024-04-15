<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['area:read']],
    denormalizationContext: ['groups' => ['area:write']],
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
    private ?Floor $floor = null;

    #[Groups(['area:read', 'area:write'])]
    private ?int $floor_id = null;

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getFloorId(): ?int
    {
        return $this->floor_id;
    }

    /**
     * @param int|null $floor_id
     */
    public function setFloorId(?int $floor_id): void
    {
        $this->floor_id = $floor_id;
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
}
