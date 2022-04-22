<?php

namespace App\Controller\Admin;

use App\Entity\Member;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

class MemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Member::class;
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            $request = Request::createFromGlobals();
            $importedFile = $request->query->get('todelete');

            if ($importedFile) {
                $uploadedMemberFileDir = $this->getParameter('importmembers_directory');
                $importedfile_fullpath = $uploadedMemberFileDir.'/'.$importedFile;

                $filesystem = new Filesystem();
                $filesystem->remove($importedfile_fullpath);
            }
        }

        return $responseParameters;
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportMembers = Action::new('exportMembers', 'Export Members', 'fa fa-file-invoice')
            ->displayAsButton()
            ->setHtmlAttributes(['target' => '_blank'])
            ->linkToUrl('google.com')
        ;

        return $actions
            ->disable(Action::DELETE)
            ->addBatchAction($exportMembers)
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
