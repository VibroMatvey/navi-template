<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\NodeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: NodeTypeRepository::class)]
#[GetCollection(
    paginationEnabled: false,
    normalizationContext: ['groups' => ['node_type:read']]
)]
class NodeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['node:read', 'node_type:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['node:read', 'node_type:read'])]
    private ?string $type = null;

    /**
     * @var Collection<int, Node>
     */
    #[ORM\ManyToMany(targetEntity: Node::class, mappedBy: 'types', cascade: ['all'])]
    private Collection $nodes;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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
}
