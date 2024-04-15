<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\DataProvider\NodeNavigateDataProvider;
use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

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
        new Post()
    ],
    normalizationContext: ['groups' => ['node:read']],
    denormalizationContext: ['groups' => ['node:write']],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: NodeRepository::class)]
class Node
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['point:read', 'node:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'nodes')]
    #[Groups(['node:read', 'node:write'])]
    private ?Point $point = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'nodes')]
    #[ApiProperty(example: ['/api/nodes/id'])]
    #[Groups(['node:read', 'node:write'])]
    private Collection $nodes;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
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
}
