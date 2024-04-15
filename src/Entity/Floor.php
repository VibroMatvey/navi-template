<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use App\Repository\FloorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['floor:read']],
    paginationEnabled: false
)]
#[ORM\Entity(repositoryClass: FloorRepository::class)]
class Floor
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['floor:read', 'point:read','node:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['floor:read', 'point:read','node:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['floor:read', 'point:read','node:read'])]
    private ?string $mapImage = null;

    #[Vich\UploadableField(mapping: 'floor_map_images', fileNameProperty: 'mapImage')]
    #[Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])]
    private ?File $mapImageFile = null;

    /**
     * @var Collection<int, Point>
     */
    #[ORM\OneToMany(mappedBy: 'floor', targetEntity: Point::class)]
    private Collection $points;

    /**
     * @var Collection<int, Area>
     */
    #[ORM\OneToMany(mappedBy: 'floor', targetEntity: Area::class)]
    private Collection $areas;

    public function __construct()
    {
        $this->points = new ArrayCollection();
        $this->areas = new ArrayCollection();
    }

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

    public function getMapImage(): ?string
    {
        return $this->mapImage;
    }

    public function setMapImage(string $mapImage): static
    {
        $this->mapImage = $mapImage;

        return $this;
    }

    public function getMapImageFile(): ?File
    {
        return $this->mapImageFile;
    }

    public function setMapImageFile(?File $mapImageFile): static
    {
        $this->mapImageFile = $mapImageFile;
        if (null !== $mapImageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
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
            $point->setFloor($this);
        }

        return $this;
    }

    public function removePoint(Point $point): static
    {
        if ($this->points->removeElement($point)) {
            // set the owning side to null (unless already changed)
            if ($point->getFloor() === $this) {
                $point->setFloor(null);
            }
        }

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
            $area->setFloor($this);
        }

        return $this;
    }

    public function removeArea(Area $area): static
    {
        if ($this->areas->removeElement($area)) {
            // set the owning side to null (unless already changed)
            if ($area->getFloor() === $this) {
                $area->setFloor(null);
            }
        }

        return $this;
    }
}
