<?php

namespace App\Service;

use App\Repository\NodeRepository;

readonly class AStarService {

    public function __construct(
        public NodeRepository $nodeRepository
    )
    {
    }

    public static function find_path($start, $goal, $routeType, $repo): ?array
    {
        $all_nodes = [];
        $start_node = new AStarNodeService($repo, $start, $all_nodes);
        $goal_node = new AStarNodeService($repo, $goal, $all_nodes);
        $closed_set = [];
        $open_set = [];
        $start_node->heuristics_estimate_path_length = self::__get_heuristics_path_length($start_node, $goal_node);
        $open_set[] = $start_node;

        while (count($open_set) > 0) {
            $current_node = null;
            foreach ($open_set as $node) {
                if ($current_node == null || $node->estimate_full_path() < $current_node->estimate_full_path()) {
                    $current_node = $node;
                }
            }

            if ($current_node->node == $goal) {
                return self::__get_path_for_node($current_node, $start_node);
            }

            unset($open_set[array_search($current_node, $open_set)]);
            $closed_set[] = $current_node;

            foreach ($current_node->neighbors as $neighbour_node) {
                if (in_array($neighbour_node, $closed_set) || !$neighbour_node->node->getTypes()->contains($routeType)) {
                    continue;
                }

                /* @var AStarNodeService $neighbour_node */

                $open_node = array_filter($open_set, function($node) use ($neighbour_node, $routeType) {
                    return $node == $neighbour_node;
                });

                if (empty($open_node)) {
                    $neighbour_node->heuristics_estimate_path_length = self::__get_heuristics_path_length($current_node, $neighbour_node);
                    $neighbour_node->from_node = $current_node;
                    $neighbour_node->path_length_from_start = $current_node->path_length_from_start + self::__get_heuristics_path_length($neighbour_node, $current_node);
                    $open_set[] = $neighbour_node;
                } elseif ($current_node->estimate_full_path() < $open_node[array_key_first($open_node)]->heuristics_estimate_path_length) {
                    $open_node[array_key_first($open_node)]->from_node = $current_node;
                    $open_node[array_key_first($open_node)]->path_length_from_start = $current_node->path_length_from_start;
                }
            }
        }
        return null;
    }

    private static function __get_path_for_node($path_node, $start_node): array
    {
        $result_nodes = [];
        $current_node = $path_node;

        while ($current_node) {
            if ($path_node == $current_node->from_node || $current_node == $start_node) {
                break;
            }
            $result_nodes[] = $current_node;
            $current_node = $current_node->from_node;
        }
        $result_nodes[] = $start_node;
        $result_nodes = array_reverse($result_nodes);

        $points = [];
        foreach ($result_nodes as $node) {
            $points[] = $node->node->getPoint();
        }
        return $points;
    }

    private static function __get_heuristics_path_length($start, $end): float
    {
        return sqrt(pow($end->node->getPoint()->getX() - $start->node->getPoint()->getX(), 2) + pow($end->node->getPoint()->getY() - $start->node->getPoint()->getY(), 2));
    }
}