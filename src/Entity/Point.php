<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Point\PointCreateController;
use App\Controller\Point\PointUpdateController;
use App\Repository\PointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: PointCreateController::class,
            denormalizationContext: ['groups' => ['point:write']],
        ),
        new Patch(
            controller: PointUpdateController::class,
            denormalizationContext: ['groups' => ['point:write']],
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['point:read']],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: PointRepository::class)]
class Point
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['point:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['point:read', 'point:write'])]
    private ?int $x = null;

    #[ORM\Column]
    #[Groups(['point:read', 'point:write'])]
    private ?int $y = null;

    #[ORM\ManyToOne(inversedBy: 'points')]
    private ?Floor $floor = null;

    /**
     * @var Collection<int, Area>
     */
    #[ORM\ManyToMany(targetEntity: Area::class, mappedBy: 'points')]
    private Collection $areas;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\OneToMany(mappedBy: 'point', targetEntity: Node::class, cascade: ['persist'])]
    private Collection $nodes;

    #[Groups(['point:write', 'point:read'])]
    private ?int $floor_id = null;

    public function __construct()
    {
        $this->areas = new ArrayCollection();
        $this->nodes = new ArrayCollection();
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

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(int $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(int $y): static
    {
        $this->y = $y;

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
     * @return Collection<int, Area>
     */
    public function getAreas(): Collection
    {
        return $this->areas;
    }

    public function addArea(Area $area): static
    {
        if (!$this->areas->contains($area)) {
            $this->areas->add($area);
            $area->addPoint($this);
        }

        return $this;
    }

    public function removeArea(Area $area): static
    {
        if ($this->areas->removeElement($area)) {
            $area->removePoint($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Node>
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(Node $node): static
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes->add($node);
            $node->setPoint($this);
        }

        return $this;
    }

    public function removeNode(Node $node): static
    {
        if ($this->nodes->removeElement($node)) {
            // set the owning side to null (unless already changed)
            if ($node->getPoint() === $this) {
                $node->setPoint(null);
            }
        }

        return $this;
    }
}
