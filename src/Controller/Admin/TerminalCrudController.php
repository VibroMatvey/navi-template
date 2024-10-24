<?php

namespace App\Controller\Admin;

use App\Entity\Terminal;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TerminalCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Terminal::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Терминалы')
            ->setEntityLabelInSingular('терминал')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление терминала')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение терминала');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('name', 'Название');
    }
}