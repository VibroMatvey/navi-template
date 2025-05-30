<?php

namespace App\User\Presentation\EasyAdmin\Crud;

use App\Shared\Application\Command\CommandBusInterface;
use App\User\Application\Command\CreateUser\CreateUserCommand;
use App\User\Application\Command\EditUser\EditUserCommand;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission('ROLE_ADMIN')
            ->setEntityLabelInPlural('Пользователи')
            ->setEntityLabelInSingular('пользователя')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление пользователя')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение пользователя')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Информация о пользователе');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermissions([
                Action::NEW => 'ROLE_ADMIN',
                Action::DELETE => 'ROLE_ADMIN',
                Action::EDIT => 'ROLE_ADMIN',
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('ulid', 'ULID')
            ->onlyOnIndex();
        yield TextField::new('username', 'Логин');
        if (Crud::PAGE_EDIT == $pageName) {
            yield TextField::new('plainPassword', 'Новый пароль')
                ->setFormTypeOption('data', null)
                ->setFormType(PasswordType::class)
                ->setRequired(false)
                ->onlyOnForms();
        } else {
            yield TextField::new('password', 'Пароль')
                ->setFormType(PasswordType::class)
                ->onlyOnForms();
        }
        yield ChoiceField::new('roles', 'Права')
            ->setRequired(true)
            ->allowMultipleChoices()
            ->renderExpanded()
            ->setChoices(User::ROLES);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            /* @var User $entityInstance */
            $this->commandBus->execute(new CreateUserCommand(
                $entityInstance->getUsername(),
                $entityInstance->getPassword(),
                $entityInstance->getRoles(),
            ));
            $this->addFlash('success', 'Добавлен новый пользователь');
        } catch (Exception $exception) {
            $this->addFlash('danger', "Ошибка при добавлении пользователя - {$exception->getMessage()}");
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            /* @var User $entityInstance */
            $this->commandBus->execute(new EditUserCommand(
                $entityInstance->getUlid(),
                $entityInstance->getUsername(),
                $entityInstance->getPlainPassword(),
                $entityInstance->getRoles(),
            ));
            $this->addFlash('success', 'Информация пользователя была обновлена');
        } catch (Exception $exception) {
            $this->addFlash('danger', "Ошибка при обновлении информации о пользователе - {$exception->getMessage()}");
        }
    }
}