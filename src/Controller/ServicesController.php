<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesController extends AbstractController
{
    #[Route('/services', name: 'services')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('services/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }
}
