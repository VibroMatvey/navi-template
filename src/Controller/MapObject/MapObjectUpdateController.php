<?php

namespace App\Controller\MapObject;

use App\Dto\MapObjectDto;
use App\Repository\AreaRepository;
use App\Repository\MapObjectRepository;
use App\Repository\NodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class MapObjectUpdateController  extends AbstractController
{
    public function __construct(
        private readonly MapObjectRepository $mapObjectRepository,
        private readonly NodeRepository $nodeRepository,
        private readonly AreaRepository $areaRepository,
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), MapObjectDto::class, 'json');

        $mapObject = $this->mapObjectRepository->find($id);

        if (!$mapObject) {
            throw new NotFoundHttpException("map object with id $id not found");
        }

        if ($body->getNodes() !== null) {
            $nodes = new ArrayCollection();
            foreach ($body->getNodes() as $node_id) {
                $node_item = $this->nodeRepository->find($node_id);
                if (!$node_item) {
                    throw new NotFoundHttpException("node with id $node_id not found");
                }
                $nodes->add($node_item);
            }
            $mapObject->setNodes($nodes);
        }

        if ($body->getAreas() !== null) {
            $areas = new ArrayCollection();
            foreach ($body->getAreas() as $area_id) {
                $area_item = $this->areaRepository->find($area_id);
                if (!$area_item) {
                    throw new NotFoundHttpException("area with id $area_id not found");
                }
                $areas->add($area_item);
            }
            $mapObject->setAreas($areas);
        }

        $this->mapObjectRepository->save($mapObject, true);

        return $this->json($mapObject, 201, [], ['groups' => 'map-object:read']);
    }
}