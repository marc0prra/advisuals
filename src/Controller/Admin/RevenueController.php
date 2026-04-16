<?php

namespace App\Controller\Admin;

use App\Repository\RevenueTrackingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class RevenueController extends AbstractController
{
    #[Route('/revenue', name: 'app_admin_revenue', methods: ['GET'])]
    public function index(Request $request, RevenueTrackingRepository $repo): Response
    {
        $period  = $request->query->get('period', 'month');
        $allowed = ['day', 'week', 'month', 'year'];

        if (!in_array($period, $allowed)) {
            $period = 'month';
        }

        $entries = $repo->findByPeriod($period);
        $stats   = $repo->getStatsByPeriod($period);

        return $this->render('admin/revenue/index.html.twig', [
            'period'  => $period,
            'entries' => $entries,
            'stats'   => $stats,
        ]);
    }
}
