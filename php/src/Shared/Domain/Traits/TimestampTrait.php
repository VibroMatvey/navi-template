<?php

namespace App\Shared\Domain\Traits;

use DateTime;
use DateTimeInterface;

trait TimestampTrait
{
    private ?DateTimeInterface $createdAt = null;
    private ?DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTime();
        }

        if ($this->updatedAt === null) {
            $this->updatedAt = new DateTime();
        }
    }

    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }
}