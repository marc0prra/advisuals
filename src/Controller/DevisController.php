<?php

namespace App\Controller;

use App\Entity\EstimateRequest;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DevisController extends AbstractController
{
    #[Route('/devis', name: 'devis', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    #[Route('/devis/submit', name: 'devis_submit', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $em, ServiceRepository $serviceRepository): Response
    {
        $clientName  = trim($request->request->get('client_name', ''));
        $clientEmail = trim($request->request->get('client_email', ''));
        $clientPhone = trim($request->request->get('client_phone', ''));
        $serviceId   = (int) $request->request->get('service_id', 0);
        $total       = (float) $request->request->get('total_estimated', 0);
        $message     = trim($request->request->get('message', ''));

        if ($clientName && $clientEmail && $message) {
            $estimateRequest = new EstimateRequest();
            $estimateRequest->setClientName($clientName);
            $estimateRequest->setClientEmail($clientEmail);
            $estimateRequest->setClientPhone($clientPhone ?: '0000000000');
            $estimateRequest->setServiceId($serviceId);
            $estimateRequest->setTotalEstimated($total);
            $estimateRequest->setMessage($message);
            $estimateRequest->setStatus('en_attente');
            $estimateRequest->setCreatedAt(new \DateTimeImmutable());

            $em->persist($estimateRequest);
            $em->flush();

            $this->addFlash('devis_success', 'Votre demande a bien été envoyée ! Nous vous contacterons sous 24h.');
        }

        return $this->redirectToRoute('devis');
    }
}
