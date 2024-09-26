<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\MapObject\MapObjectUpdateController;
use App\Dto\MapObjectDto;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\MapObjectRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
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
    use UpdatedAtTrait;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['map-object:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['map-object:read'])]
    private ?int $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['map-object:read'])]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'map_object_images', fileNameProperty: 'image')]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])]
    private ?File $imageFile = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }
}
