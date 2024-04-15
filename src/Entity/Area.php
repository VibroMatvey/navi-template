<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
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
        new Post()
    ],
    normalizationContext: ['groups' => ['area:read']],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['area:read', 'node:read'])]
    private ?int $id = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\ManyToMany(targetEntity: Point::class, inversedBy: 'areas')]
    #[ApiProperty(example: ['/api/points/id'])]
    #[Groups(['area:read', 'node:read'])]
    private Collection $points;

    #[ORM\ManyToOne(inversedBy: 'areas')]
    #[ApiProperty(example: '/api/floors/id')]
    #[Groups(['area:read', 'node:read'])]
    private ?Floor $floor = null;

    public function __construct()
    {
        $this->points = new ArrayCollection();
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
