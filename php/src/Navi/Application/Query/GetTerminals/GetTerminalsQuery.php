<?php

namespace App\Navi\Application\Query\GetTerminals;

use App\Shared\Application\Query\QueryInterface;

final readonly class GetTerminalsQuery implements QueryInterface
{
    public function __construct(
        public ?string $mapUlid,
    )
    {
    }
}