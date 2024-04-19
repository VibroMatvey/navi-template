<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\Terminal\TerminalUpdateController;
use App\Dto\TerminalDto;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\TerminalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Patch(
            controller: TerminalUpdateController::class,
            input: TerminalDto::class
        )
    ],
    normalizationContext: ['groups' => 'terminal:read'],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: TerminalRepository::class)]
class Terminal
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('terminal:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('terminal:read')]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'terminals')]
    private ?Node $node = null;

    #[ORM\ManyToOne(inversedBy: 'terminals')]
    private ?Area $area = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNode(): ?Node
    {
        return $this->node;
    }

    public function setNode(?Node $node): static
    {
        $this->node = $node;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): static
    {
        $this->area = $area;

        return $this;
    }
}
