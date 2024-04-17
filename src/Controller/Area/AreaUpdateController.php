<?php

namespace App\Controller\Area;

use App\Dto\AreaDto;
use App\Repository\AreaRepository;
use App\Repository\FloorRepository;
use App\Repository\NodeRepository;
use App\Repository\PointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class AreaUpdateController extends AbstractController
{
    public function __construct(
        private readonly PointRepository $pointRepository,
        private readonly FloorRepository $floorRepository,
        private readonly AreaRepository $areaRepository,
    )
    {
    }

    public function __invoke(Request $request, SerializerInterface  $serializer): JsonResponse
    {
        $id = $request->attributes->all()['id'];

        $body = $serializer->deserialize($request->getContent(), AreaDto::class, 'json');

        $area = $this->areaRepository->find($id);

        if (!$area) {
            throw new NotFoundHttpException("area with id $id not found");
        }

        if ($body->getFloor()) {
            $floor = $this->floorRepository->find($body->getFloor());

            if (!$floor) {
                $floor_id= $body->getFloor();
                throw new NotFoundHttpException("floor with id $floor_id not found");
            }

            $area->setFloor($floor);
        }

        if (count($body->getPoints())) {
            $points = new ArrayCollection();
            foreach ($body->getPoints() as $point_id) {
                $point_item = $this->pointRepository->find($point_id);
                if (!$point_item) {
                    throw new NotFoundHttpException("point with id $point_id not found");
                }
                $points->add($point_item);
            }

            $area->setPoints($points);
        }

        $this->areaRepository->save($area, true);

        return $this->json($area, 201, [], ['groups' => 'area:read']);
    }
}