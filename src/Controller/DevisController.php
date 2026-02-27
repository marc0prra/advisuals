<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DevisController extends AbstractController
{
    #[Route('/devis', name: 'devis')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }
}
