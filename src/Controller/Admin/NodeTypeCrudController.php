<?php

namespace App\Controller\Admin;

use App\Entity\NodeType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NodeTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return NodeType::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Типы маршрутов')
            ->setEntityLabelInSingular('тип маршрута')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление типа маршрута')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение типа маршрута')
            ->setPageTitle(Crud::PAGE_DETAIL, "Информация о типе маршрута");
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

        yield TextField::new('type', 'Тип');
    }
}
