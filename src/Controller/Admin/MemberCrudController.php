<?php

namespace App\Controller\Admin;

use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Member::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('firstName')->setColumns(6),
            TextField::new('lastName')->setColumns(6),
            TextField::new('telephone1', 'Primary phone number')->setColumns(6),
            TextField::new('telephone2', 'Secondary phone number')->setColumns(6),
            TextareaField::new('address')->setDefaultColumns(12),
            AssociationField::new('section')->setDefaultColumns(12),
            DateTimeField::new('created')->onlyOnDetail(),
            AssociationField::new('createdBy')->hideOnForm(),
            DateTimeField::new('updated')->onlyOnDetail(),
            AssociationField::new('updatedBy')->hideOnForm(),
        ];
    }
}
