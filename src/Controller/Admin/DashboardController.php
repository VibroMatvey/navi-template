<?php

namespace App\Controller\Admin;

use App\Entity\Floor;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Админ-панель')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Навигация');
        yield MenuItem::linkToCrud('Этажи', 'fas fa-stairs', Floor::class);
        yield MenuItem::section('Настройки');
        yield MenuItem::linkToCrud('Пользователи', 'fa fa-user-cog', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('API', 'fa fa-link', '/api')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');

    }
}
