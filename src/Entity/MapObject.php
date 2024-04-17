<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\MapObject\MapObjectUpdateController;
use App\Dto\MapObjectDto;
use App\Repository\MapObjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[UniqueEntity(['title'])]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Patch(
            controller: MapObjectUpdateController::class,
            input: MapObjectDto::class
        )
    ],
    normalizationContext: ['groups' => ['map-object:read']],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: MapObjectRepository::class)]
class MapObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['map-object:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['map-object:read'])]
    private ?string $title = null;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\ManyToMany(targetEntity: Node::class, inversedBy: 'mapObjects')]
    #[Groups(['map-object:read'])]
    private Collection $nodes;

    /**
     * @var Collection<int, Area>
     */
    #[ORM\ManyToMany(targetEntity: Area::class, inversedBy: 'mapObjects')]
    #[Groups(['map-object:read'])]
    private Collection $areas;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
        $this->areas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
        }

        return $this;
    }

    public function removeNode(Node $node): static
    {
        $this->nodes->removeElement($node);

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
        }

        return $this;
    }

    public function removeArea(Area $area): static
    {
        $this->areas->removeElement($area);

        return $this;
    }

    /**
     * @param Collection $areas
     */
    public function setAreas(Collection $areas): void
    {
        $this->areas = $areas;
    }

    /**
     * @param Collection $nodes
     */
    public function setNodes(Collection $nodes): void
    {
        $this->nodes = $nodes;
    }
}
