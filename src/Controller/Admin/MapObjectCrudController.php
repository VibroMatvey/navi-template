<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichGalleryField;
use App\Entity\MapObject;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            ->setEntityLabelInSingular('объект карты')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление объекта карты')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение объекта карты')
            ->setPageTitle(Crud::PAGE_DETAIL, "Информация об объекте карты");
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield TextField::new('title', 'Название');
        yield TextEditorField::new('description', 'Описание');

        yield CollectionField::new('images', 'Изображения')
            ->setRequired(false)
            ->showEntryLabel(false)
            ->useEntryCrudForm(MapObjectImageCrudController::class)
            ->onlyOnForms()
            ->setColumns(8);

        if (Crud::PAGE_EDIT === $pageName) {
            yield AssociationField::new('mapObjects', 'Объекты')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder
                        ->leftJoin('entity.mapObjects', 'mapObject')
                        ->andWhere('mapObject.id IS NULL')
                        ->andWhere('entity.id != :id')
                        ->setParameter('id', $this->getContext()->getEntity()?->getInstance()?->getId())
                )
                ->onlyOnForms()
                ->setHelp('Если выбраны дочерние объекты карты, родительский объект считается комплексом');
        }

        if (Crud::PAGE_NEW === $pageName) {
            yield AssociationField::new('mapObjects', 'Объекты')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                ->setQueryBuilder(
                    fn(QueryBuilder $queryBuilder) => $queryBuilder
                        ->leftJoin('entity.mapObjects', 'mapObject')
                        ->andWhere('mapObject.id IS NULL')
                )
                ->onlyOnForms()
                ->setHelp('Если выбраны дочерние объекты карты, родительский объект считается комплексом');
        }

        yield ChoiceField::new('type', 'Тип')
            ->hideOnForm()
            ->setChoices([
                'Комплекс' => 'complex',
                'Одиночный' => 'object'
            ]);

        yield VichGalleryField::new('images.image', 'Изображения')
            ->hideOnForm();
    }
}
