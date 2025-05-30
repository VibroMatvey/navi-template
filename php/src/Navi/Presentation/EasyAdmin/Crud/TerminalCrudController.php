<?php

namespace App\Navi\Presentation\EasyAdmin\Crud;

use App\Navi\Application\Command\DeleteTerminal\DeleteTerminalCommand;
use App\Navi\Domain\Entity\Terminal;
use App\Shared\Application\Command\CommandBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TerminalCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Terminal::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission('ROLE_ADMIN')
            ->setEntityLabelInPlural('Терминалы')
            ->setEntityLabelInSingular('терминал')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить терминал')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать терминал')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Информация о терминале');
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
        yield TextField::new('ulid', 'Идентификатор')
            ->onlyOnIndex();
        yield TextField::new('name', 'Наименование');
        yield DateTimeField::new('createdAt', 'Создан в')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Обновлен в')
            ->hideOnForm();
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->commandBus->execute(new DeleteTerminalCommand($entityInstance->getUlid()));
    }
}