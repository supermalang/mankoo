<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportMemberController extends AbstractController
{
    #[Route('/admin/member', name: 'app_import_member')]
    public function index(): Response
    {
        return $this->render('import_member/index.html.twig', [
            'controller_name' => 'ImportMemberController',
            'page_name' => 'Import Member',
        ]);
    }
}
