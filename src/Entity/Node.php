<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Node\NodeCreateController;
use App\Controller\Node\NodeUpdateController;
use App\DataProvider\NodeNavigateDataProvider;
use App\Dto\NodeDto;
use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ApiResource(
    operations: [
        new Get(),
        new Get(
            'navigate',
            openapi: new Operation(
                parameters: [
                    [
                        'name' => 'from',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'integer'
                    ],
                    [
                        'name' => 'to',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'integer'
                    ],
                ]
            ),
            provider: NodeNavigateDataProvider::class
        ),
        new GetCollection(),
        new Post(
            controller: NodeCreateController::class,
            input: NodeDto::class
        ),
        new Patch(
            controller: NodeUpdateController::class,
            input: NodeDto::class
        ),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['node:read']],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: NodeRepository::class)]
class Node
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['node:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['all'], inversedBy: 'nodes')]
    #[Groups(['node:read'])]
    private ?Point $point = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'nodes')]
    private Collection $nodes;

    /**
     * @var Collection<int, MapObject>
     */
    #[ORM\OneToMany(mappedBy: 'node', targetEntity: MapObject::class)]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private Collection $mapObjects;

    #[ORM\OneToMany(mappedBy: 'node', targetEntity: Terminal::class)]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private Collection $terminals;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
        $this->mapObjects = new ArrayCollection();
        $this->terminals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPoint(): ?Point
    {
        return $this->point;
    }

    public function setPoint(?Point $point): static
    {
        $this->point = $point;

        return $this;
    }

    /**
     * @param Collection $nodes
     */
    public function setNodes(Collection $nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @return Collection<int, self>
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }

    public function addNode(self $node): static
    {
        if (!$this->nodes->contains($node)) {
            $this->nodes->add($node);
        }

        return $this;
    }

    public function removeNode(self $node): static
    {
        $this->nodes->removeElement($node);

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
            $mapObject->setNode($this);
        }

        return $this;
    }

    public function removeMapObject(MapObject $mapObject): static
    {
        if ($this->mapObjects->removeElement($mapObject)) {
            // set the owning side to null (unless already changed)
            if ($mapObject->getNode() === $this) {
                $mapObject->setNode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Terminal>
     */
    public function getTerminals(): Collection
    {
        return $this->terminals;
    }

    public function addTerminal(Terminal $terminal): static
    {
        if (!$this->terminals->contains($terminal)) {
            $this->terminals->add($terminal);
            $terminal->setNode($this);
        }

        return $this;
    }

    public function removeTerminal(Terminal $terminal): static
    {
        if ($this->terminals->removeElement($terminal)) {
            // set the owning side to null (unless already changed)
            if ($terminal->getNode() === $this) {
                $terminal->setNode(null);
            }
        }

        return $this;
    }
}
