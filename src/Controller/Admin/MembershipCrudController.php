<?php

namespace App\Controller\Admin;

use App\Entity\Membership;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MembershipCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Membership::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
