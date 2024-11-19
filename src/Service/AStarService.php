<?php

namespace App\Service;

use App\Entity\Node;
use App\Entity\NodeType;
use DateInterval;
use DateTimeImmutable;
use Exception;
use SplPriorityQueue;

final readonly class AStarService
{
    /**
     * @throws Exception
     */
    public function find_path(
        Node     $start,
        Node     $goal,
        NodeType $routeType,
        array    $allNodes
    ): ?array
    {
        $openSet = new SplPriorityQueue();
        $openSet->insert($start, 0);

        $cameFrom = [];
        $gScore = [$start->getId() => 0];
        $fScore = [$start->getId() => $this->heuristic($start, $goal)];

        while (!$openSet->isEmpty()) {
            $current = $openSet->extract();

            if ($current === $goal) {
                return $this->reconstructPath($cameFrom, $current, $routeType);
            }

            /* @var Node $node */
            foreach ($allNodes as $node) {
                $neighbor = $node->getNodes()->contains($current);

                if (!$neighbor || !$node->getTypes()->contains($routeType)) {
                    continue;
                }

                $tentativeGScore = $gScore[$current->getId()] + 1;

                if (!isset($gScore[$node->getId()]) || $tentativeGScore < $gScore[$node->getId()]) {
                    $cameFrom[$node->getId()] = $current;
                    $gScore[$node->getId()] = $tentativeGScore;
                    $fScore[$node->getId()] = $tentativeGScore / 10 + $this->heuristic($node, $goal);

                    if (!$this->inOpenSet($openSet, $node)) {
                        $openSet->insert($node, -$fScore[$node->getId()]);
                    }
                }
            }
        }

        return [];
    }

    private function heuristic(Node $a, Node $b): float
    {
        return sqrt(
            pow($a->getPoint()->getX() - $b->getPoint()->getX(), 2) +
            pow($a->getPoint()->getY() - $b->getPoint()->getY(), 2)
        );
    }

    /**
     * @throws Exception
     */
    private function reconstructPath(array $cameFrom, Node $current, NodeType $routeType): array
    {
        $meters = 0;
        $totalPath = [$current->getPoint()];
        $previousNode = $current;
        while (isset($cameFrom[$current->getId()])) {
            $current = $cameFrom[$current->getId()];
            $totalPath[] = $current->getPoint();
            $pixels = $this->heuristic($previousNode, $current);
            if ($current->getPoint()->getFloor()->getPixelsPerMeter()) {
                $meters += $pixels / $current->getPoint()->getFloor()->getPixelsPerMeter();
            }
            $previousNode = $current;
        }
        $speed = $routeType->getSpeed() / 3.6;
        $timeInSeconds = $meters / $speed;
        $startDateTime = new DateTimeImmutable('@0');
        $time = $startDateTime->add(new DateInterval('PT' . (int)$timeInSeconds . 'S'));

        return [
            'points' => array_reverse($totalPath),
            'distance' => round($meters, 2),
            'time' => $time,
        ];
    }

    private function inOpenSet(SplPriorityQueue $openSet, Node $node): bool
    {
        foreach (clone $openSet as $item) {
            if ($item === $node) {
                return true;
            }
        }
        return false;
    }
}