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

        if ($body->getNode() !== null) {
            $nodeId = $body->getNode();
            $node = $this->nodeRepository->find($nodeId);
            if (!$node) {
                throw new NotFoundHttpException("node with id $nodeId not found");
            }
            $mapObject->setNode($node);
        }

        if ($body->getArea() !== null) {
            $areaId = $body->getArea();
            $area = $this->areaRepository->find($areaId);
            if (!$area) {
                throw new NotFoundHttpException("node with id $areaId not found");
            }
            $mapObject->setArea($area);
        }

        $this->mapObjectRepository->save($mapObject, true);

        return $this->json($mapObject, 201, [], ['groups' => 'map-object:read']);
    }
}