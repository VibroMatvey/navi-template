<?php

namespace App\Shared\Presentation\EasyAdmin\Crud;

use App\Navi\Domain\Entity\Map;
use App\Navi\Domain\Entity\POI;
use App\Navi\Domain\Entity\Terminal;
use App\User\Domain\Entity\User;
use App\User\Presentation\EasyAdmin\Crud\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
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
                    Админка
            ')
            ->generateRelativeUrls()
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Навигация')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Терминалы', 'fas fa-tv', Terminal::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Точки интереса', 'fas fa-map-location-dot', POI::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Карты', 'fas fa-map', Map::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('Редактор карт', 'fa fa-pencil', '/navi/docs/index.html')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('API Навигации', 'fa fa-link', '/navi/docs/index.html')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::section('Настройки')
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Пользователи', 'fas fa-user-gear', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('API Админки', 'fa fa-link', '/api')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');
    }
}
