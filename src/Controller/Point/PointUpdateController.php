<?php

namespace App\Controller\Point;

use App\Entity\Point;
use App\Repository\FloorRepository;
use App\Repository\PointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class PointUpdateController extends AbstractController
{
    public function __construct(
        private readonly PointRepository $pointRepository,
        private readonly FloorRepository $floorRepository
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->attributes->all()['id'];
        $body = json_decode($request->getContent(), true);


        $point = $this->pointRepository->find($id);

        if (!$point) {
            throw new NotFoundHttpException("point with id $id not found");
        }

        if (key_exists('floor_id', $body)) {
            $floor = $this->floorRepository->find($body['floor_id']);

            if (!$floor) {
                throw new NotFoundHttpException("floor with id $body[floor_id] not found");
            }

            $point->setFloor($floor);
        }

        if (key_exists('x', $body)) {
            $point->setX($body['x']);
        }

        if (key_exists('y', $body)) {
            $point->setY($body['y']);
        }

        $this->pointRepository->save($point, true);

        return $this->json($point, 201, [], ['groups' => 'point:read']);
    }
}