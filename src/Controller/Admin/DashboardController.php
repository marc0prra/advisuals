<?php

namespace App\Controller\Admin;

use App\Repository\EstimateRequestRepository;
use App\Repository\RevenueTrackingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(
        EstimateRequestRepository $estimateRepo,
        RevenueTrackingRepository $revenueRepo
    ): Response {
        $pendingCount   = $estimateRepo->countPending();
        $recentRequests = $estimateRepo->findRecent(5);
        $monthStats     = $revenueRepo->getStatsCurrentMonth();

        return $this->render('admin/dashboard/index.html.twig', [
            'pending_count'   => $pendingCount,
            'recent_requests' => $recentRequests,
            'month_stats'     => $monthStats,
        ]);
    }
}
