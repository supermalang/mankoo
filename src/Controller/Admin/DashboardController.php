<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Member;
use App\Entity\Membership;
use App\Entity\Section;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(ChartBuilderInterface $chartBuilder = null): Response
    {
        assert(null !== $chartBuilder);

        // $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        // $chart->setData( ]);

        // $chart->setOptions([ ]);

        return $this->render('admin/home-dashboard.html.twig', [
            // 'chart' => $chart,
        ]);

        return parent::index();
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());
        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mankoo app')
            ->generateRelativeUrls()
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToCrud('Members', 'fa fa-question-circle', Member::class);

        yield MenuItem::linkToCrud('Membership fees', 'fas fa-comments', Membership::class);

        yield MenuItem::linkToCrud('Sections', 'fas fa-folder', Section::class);

        yield MenuItem::linkToCrud('Users', 'fas fa-users', Admin::class);
    }
}
