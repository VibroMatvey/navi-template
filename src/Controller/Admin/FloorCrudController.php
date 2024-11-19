<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichImageField;
use App\Entity\Floor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FloorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Floor::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Локации')
            ->setEntityLabelInSingular('Локацию')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление локации')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение локации')
            ->setPageTitle(Crud::PAGE_DETAIL, "Информация о локации");
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('name', 'Локация');

        $imageCard = VichImageField::new('mapImageFile', 'Изображение')
            ->setHelp('
                <div class="mt-3">
                    <span class="badge badge-info">*.jpg</span>
                    <span class="badge badge-info">*.jpeg</span>
                    <span class="badge badge-info">*.png</span>
                    <span class="badge badge-info">*.webp</span>
                </div>
            ')
            ->onlyOnForms()
            ->setFormTypeOption('allow_delete', false)
            ->setRequired(true);

        if (Crud::PAGE_EDIT == $pageName) {
            $imageCard->setRequired(false);
        }

        yield $imageCard;

        yield VichImageField::new('mapImage', 'Изображение')
            ->hideOnForm();

        yield NumberField::new('pixelsPerMeter', 'Кол-во пикселей на метр');
    }
}
