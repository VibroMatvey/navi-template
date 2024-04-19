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

    #[ORM\ManyToOne(inversedBy: 'mapObjects')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?Node $node = null;

    #[ORM\ManyToOne(inversedBy: 'mapObjects')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?Area $area = null;

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
