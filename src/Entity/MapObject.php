<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\MapObject\MapObjectUpdateController;
use App\DataProvider\MapObjectDataProvider;
use App\Dto\MapObjectDto;
use App\Repository\MapObjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            provider: MapObjectDataProvider::class
        ),
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['map-object:read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, MapObjectImage>
     */
    #[ORM\OneToMany(mappedBy: 'mapObject', targetEntity: MapObjectImage::class, cascade: ['all'], orphanRemoval: true)]
    #[Groups(['map-object:read'])]
    private Collection $images;

    #[Groups(['map-object:read'])]
    private ?string $type = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'mapObjects')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?self $object = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'object', targetEntity: self::class)]
    #[Groups(['map-object:read'])]
    private Collection $mapObjects;

    public function __toString(): string
    {
        return $this->title;
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->mapObjects = new ArrayCollection();
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

    /**
     * @return Collection<int, MapObjectImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MapObjectImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setMapObject($this);
        }

        return $this;
    }

    public function removeImage(MapObjectImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getMapObject() === $this) {
                $image->setMapObject(null);
            }
        }

        return $this;
    }

    public function getObject(): ?self
    {
        return $this->object;
    }

    public function setObject(?self $object): static
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getMapObjects(): Collection
    {
        return $this->mapObjects;
    }

    public function addMapObject(self $mapObject): static
    {
        if (!$this->mapObjects->contains($mapObject)) {
            $this->mapObjects->add($mapObject);
            $mapObject->setObject($this);
        }

        return $this;
    }

    public function removeMapObject(self $mapObject): static
    {
        if ($this->mapObjects->removeElement($mapObject)) {
            // set the owning side to null (unless already changed)
            if ($mapObject->getObject() === $this) {
                $mapObject->setObject(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->mapObjects->count() ? 'complex' : 'object';
    }
}
