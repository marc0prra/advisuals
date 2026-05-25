<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\SiteSettingsRepository;
use App\Repository\TestimonialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        ProjectRepository $projectRepository,
        SiteSettingsRepository $settingsRepo,
        TestimonialRepository $testimonialRepo
    ): Response {
        $featuredProjects = $projectRepository->findBy(
            ['isFeatured' => true],
            ['featuredPosition' => 'ASC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'projects'     => $featuredProjects,
            'settings'     => $settingsRepo->getOrCreate(),
            'testimonials' => $testimonialRepo->findFeatured(),
        ]);
    }
}
