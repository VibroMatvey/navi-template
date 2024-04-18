<?php

namespace App\Service;

use App\Entity\Node;
use App\Repository\NodeRepository;

class AStarNodeService {
    public ?Node $node = null;
    public ?self $from_node = null;
    public ?float $heuristics_estimate_path_length = null;
    public ?array $neighbors = null;
    public ?float $path_length_from_start = null;

    public function __construct(
        private readonly NodeRepository $nodeRepository,
        $node,
        $all_nodes,
    ) {
        $this->node = $node;
        $this->path_length_from_start = 0;
        $this->heuristics_estimate_path_length = 0;
        $this->neighbors = $this->get_neighbors($all_nodes);
        $this->from_node = null;
    }

    public function get_neighbors($all_nodes): ?array
    {
        $this->neighbors = [];
        foreach ($this->node->getNodes() as $node) {
            $all_nodes[] = $this;
            $linked_node = array_filter($all_nodes, function($x) use ($node) {
                return $x->node == $node;
            });
            if (!empty($linked_node)) {
                $this->neighbors[] = $linked_node[array_key_first($linked_node)];
                continue;
            }
            $this->neighbors[] = new AStarNodeService($this->nodeRepository, $node, $all_nodes);
        }
        return $this->neighbors;
    }

    public function estimate_full_path() {
        return $this->path_length_from_start + $this->heuristics_estimate_path_length;
    }
}