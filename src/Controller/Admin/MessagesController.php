<?php

namespace App\Controller\Admin;

use App\Repository\ContactMessageRepository;
use App\Repository\EstimateRequestRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class MessagesController extends AbstractController
{
    #[Route('/messages', name: 'app_admin_messages', methods: ['GET'])]
    public function index(
        EstimateRequestRepository $repo,
        ServiceRepository $serviceRepo,
        ContactMessageRepository $contactRepo
    ): Response {
        $requests = $repo->findBy([], ['created_at' => 'DESC']);
        $contacts = $contactRepo->findBy([], ['createdAt' => 'DESC']);

        $services = [];
        foreach ($serviceRepo->findAll() as $service) {
            $services[$service->getId()] = $service->getLabel();
        }

        return $this->render('admin/messages/index.html.twig', [
            'requests' => $requests,
            'services' => $services,
            'contacts' => $contacts,
        ]);
    }
}
