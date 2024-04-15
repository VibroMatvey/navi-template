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
class PointCreateController extends AbstractController
{
    public function __construct(
        private readonly PointRepository $pointRepository,
        private readonly FloorRepository $floorRepository
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent());

        $x = $body->x;
        $y = $body->y;
        $floor_id = $body->floor_id;

        $floor = $this->floorRepository->find($floor_id);

        if (!$floor) {
            throw new NotFoundHttpException("floor with id $floor_id not found");
        }

        $point = new Point();
        $point->setX($x);
        $point->setY($y);
        $point->setFloor($floor);

        $this->pointRepository->save($point, true);

        return $this->json($point, 201, [], ['groups' => 'point:read']);
    }
}