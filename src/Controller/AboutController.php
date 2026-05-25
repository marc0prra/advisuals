<?php

namespace App\Controller;

use App\Repository\ApproachStepRepository;
use App\Repository\SiteSettingsRepository;
use App\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'about')]
    public function index(
        SiteSettingsRepository $settingsRepo,
        SkillRepository $skillRepo,
        ApproachStepRepository $approachRepo
    ): Response {
        return $this->render('about/index.html.twig', [
            'settings' => $settingsRepo->getOrCreate(),
            'skills'   => $skillRepo->findBy([], ['position' => 'ASC']),
            'steps'    => $approachRepo->findBy([], ['position' => 'ASC']),
        ]);
    }
}
