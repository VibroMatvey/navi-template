<?php

namespace App\User\Presentation\EasyAdmin\Crud;

use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\User\Application\Command\CreateUser\CreateUserCommand;
use App\User\Application\Query\GetUserByUsername\GetUserByUsernameQuery;
use App\User\Domain\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface   $queryBus,
    )
    {
    }

    #[Route(path: '/admin/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        try {
            $this->queryBus->execute(new GetUserByUsernameQuery('admin'));
        } catch (Exception) {
            $this->commandBus->execute(new CreateUserCommand('admin', 'foo', User::ROLES));
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'translation_domain' => 'admin',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin'),
            'username_label' => 'Логин',
            'password_label' => 'Пароль',
            'sign_in_label' => 'Вход',
            'forgot_password_enabled' => false,
            'forgot_password_path' => '#',
            'forgot_password_label' => 'Забыли пароль?',
            'remember_me_enabled' => true,
            'remember_me_parameter' => 'custom_remember_me_param',
            'remember_me_checked' => true,
            'remember_me_label' => 'Запомнить',
        ]);
    }

    #[Route(path: '/admin/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
