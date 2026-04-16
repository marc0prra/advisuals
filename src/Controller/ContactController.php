<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('contact_success', 'Votre message a bien été envoyé. Nous vous répondrons sous 24h.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig');
    }
}
