<?php

namespace App\Navi\Application\Command\DeletePOI;

use App\Shared\Application\Command\CommandInterface;

final readonly class DeletePOICommand implements CommandInterface
{
    public function __construct(
        public string $poUlid,
    )
    {
    }
}