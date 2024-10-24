<?php

namespace App\Controller\Admin;

use App\Entity\Floor;
use App\Entity\MapObject;
use App\Entity\NodeType;
use App\Entity\Settings;
use App\Entity\Standby;
use App\Entity\Terminal;
use App\Entity\User;
use App\Repository\FloorRepository;
use App\Repository\SettingsRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private readonly FloorRepository $floorRepository
    )
    {
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    #[Route(path: '/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('
                <span>Админ-панель</span>
            ')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Навигация');
        yield MenuItem::linkToCrud('Локации', 'fas fa-map', Floor::class);
        yield MenuItem::linkToCrud('Терминалы', 'fas fa-terminal', Terminal::class);
        yield MenuItem::linkToCrud('Объекты карты', 'fas fa-location-dot', MapObject::class);
        yield MenuItem::linkToCrud('Типы маршрутов', 'fas fa-route', NodeType::class);
        yield MenuItem::section('Настройки')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Пользователи', 'fas fa-user-gear', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('API', 'fa fa-link', '/api')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');

    }
}
