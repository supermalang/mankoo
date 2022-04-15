<?php

namespace App\Controller\Admin;

use App\Entity\Membership;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class MembershipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Membership::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('created')->onlyOnDetail(),
            AssociationField::new('createdBy')->hideOnForm(),
            DateTimeField::new('updated')->onlyOnDetail(),
            AssociationField::new('updatedBy')->hideOnForm(),
        ];
    }
}
