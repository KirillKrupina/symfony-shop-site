<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller\Admin
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'admin_dashboard_show')]
    public function dashboard(): Response
    {
        return $this->render(
            'admin/pages/dashboard.html.twig'
        );
    }
}
