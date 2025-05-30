<?php

namespace App\Shared\Domain\Traits;

use App\Shared\Domain\Service\UtilsService;

trait UlidTrait
{
    private string $ulid;

    public function getUlid(): string
    {
        return $this->ulid;
    }

    public function onPrePersistUlid(): void
    {
        $this->ulid = UtilsService::generateUlid();
    }
}