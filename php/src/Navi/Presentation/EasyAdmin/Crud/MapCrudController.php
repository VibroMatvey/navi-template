<?php

namespace App\Navi\Presentation\EasyAdmin\Crud;

use App\Navi\Domain\Entity\Map;
use App\Shared\Domain\Service\FileManagerService;
use App\Shared\Presentation\EasyAdmin\Field\Helpers\ImageHelperField;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Filesystem\Path;

class MapCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly FileManagerService $fileManagerService,
    )
    {
        $this->fileManagerService->ensureDirectoryExists(Map::UPLOAD_DIR);
    }

    public static function getEntityFqcn(): string
    {
        return Map::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityPermission('ROLE_ADMIN')
            ->setEntityLabelInPlural('Карты')
            ->setEntityLabelInSingular('карту')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавить карту')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать карту')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Информация о карте');
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
        yield ImageHelperField::new('image', 'Карта', Map::UPLOAD_DIR, ['image/*'])
            ->hideOnIndex()
            ->setFormTypeOption('allow_delete', Crud::PAGE_NEW == $pageName)
            ->setRequired(Crud::PAGE_NEW == $pageName)
            ->setColumns(5);
        yield DateTimeField::new('createdAt', 'Создан в')
            ->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Обновлен в')
            ->hideOnForm();
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $uploadDir = $this->fileManagerService->ensureDirectoryExists(Map::UPLOAD_DIR);
        $this->fileManagerService->removeFiles(Path::join($uploadDir, $entityInstance->getMap()));
        parent::deleteEntity($entityManager, $entityInstance);
    }
}