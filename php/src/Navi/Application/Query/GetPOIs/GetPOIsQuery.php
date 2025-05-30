<?php

namespace App\Navi\Application\Query\GetPOIs;

use App\Shared\Application\Query\QueryInterface;

final readonly class GetPOIsQuery implements QueryInterface
{
    public function __construct(
        public ?string $mapUlid
    )
    {
    }
}