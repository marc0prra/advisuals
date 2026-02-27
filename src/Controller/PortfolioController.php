<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortfolioController extends AbstractController
{
    #[Route('/portfolio', name: 'portfolio')]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('portfolio/index.html.twig', [
            // Récupère tous les projets pour la galerie complète
            'projects' => $projectRepository->findAll(),
        ]);
    }
}
