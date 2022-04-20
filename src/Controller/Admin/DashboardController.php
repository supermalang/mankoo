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
            ->setTitle('<img src="/img/logo.png" class="w-25"> Mankoo <span class="small">app</span>')
            ->generateRelativeUrls()
            ->setFaviconPath('/favicon.png')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-gauge-high');

        yield MenuItem::subMenu('Members', 'fa-solid fa-people-carry-box')->setSubItems([
            MenuItem::linkToCrud('Members', 'fa-solid fa-people-group', Member::class),
            MenuItem::linktoRoute('Import Members', 'fa-solid fa-upload', 'app_import_member'),
        ]);

        // yield MenuItem::linktoRoute('Stats', 'fa fa-chart-bar', 'app_import_member');

        yield MenuItem::linkToCrud('Contributions', 'fa-solid fa-sack-dollar', Membership::class);

        yield MenuItem::linkToCrud('Sections', 'fa-solid fa-sitemap', Section::class);

        yield MenuItem::section('Advanced');

        yield MenuItem::linkToCrud('Users', 'fa-solid fa-user-gear', Admin::class);
    }
}
