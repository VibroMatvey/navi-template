<?php

namespace App\Navi\Infrastructure\Service;

use App\Navi\Domain\Service\NaviServiceInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class NaviService implements NaviServiceInterface
{
    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
        private string      $naviBaseUrl,
    )
    {
        $this->client = $client->withOptions([
            'base_uri' => $this->naviBaseUrl,
        ]);
    }

    public function deleteArea(int $areaId): void
    {
        try {
            $this->client->request('DELETE', 'api/v1/areas/' . $areaId);
            return;
        } catch (Exception $e) {
//            throw new RuntimeException($e->getMessage());
        }
    }

    public function deletePoint(int $pointId): void
    {
        try {
            $this->client->request('DELETE', 'api/v1/points/' . $pointId);
            return;
        } catch (Exception $e) {
//            throw new RuntimeException($e->getMessage());
        }
    }

    public function deleteTerminal(int $terminalId): void
    {
        try {
            $this->client->request('DELETE', 'api/v1/terminals/' . $terminalId);
            return;
        } catch (Exception $e) {
//            throw new RuntimeException($e->getMessage());
        }
    }
}