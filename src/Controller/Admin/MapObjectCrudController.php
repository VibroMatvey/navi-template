<?php

namespace App\Controller\Admin;

use App\Entity\MapObject;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MapObjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MapObject::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Объекты карты')
            ->setEntityLabelInSingular('Объект карты')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление объекта карты')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение объекта карты')
            ->setPageTitle(Crud::PAGE_DETAIL, "Информация об объекте карты");
    }


    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        return parent::configureActions($actions);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('title', 'Название');
    }
}
