<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(ProjectRepository $projectRepository): Response
    {
        $featuredProjects = $projectRepository->findBy(['isFeatured' => true], ['id' => 'DESC'], 4);

        return $this->render('home/index.html.twig', [
            'projects' => $featuredProjects,
        ]);
    }
}
