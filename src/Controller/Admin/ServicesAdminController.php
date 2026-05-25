<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/services')]
#[IsGranted('ROLE_ADMIN')]
class ServicesAdminController extends AbstractController
{
    #[Route('', name: 'admin_services_index', methods: ['GET'])]
    public function index(ServiceRepository $repo): Response
    {
        return $this->render('admin/services/index.html.twig', [
            'services' => $repo->findBy([], ['category' => 'ASC', 'label' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_services_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $service = new Service();
        return $this->handleForm($service, $request, $em, true);
    }

    #[Route('/{id}/edit', name: 'admin_services_edit', methods: ['GET', 'POST'])]
    public function edit(Service $service, Request $request, EntityManagerInterface $em): Response
    {
        return $this->handleForm($service, $request, $em, false);
    }

    #[Route('/{id}/delete', name: 'admin_services_delete', methods: ['POST'])]
    public function delete(Service $service, EntityManagerInterface $em): Response
    {
        $em->remove($service);
        $em->flush();

        $this->addFlash('success', 'Service supprimé.');
        return $this->redirectToRoute('admin_services_index');
    }

    private function handleForm(Service $service, Request $request, EntityManagerInterface $em, bool $isNew): Response
    {
        if ($request->isMethod('POST')) {
            $service->setLabel(trim($request->request->get('label', '')));
            $service->setBasePrice((float) $request->request->get('base_price', 0));
            $service->setDescription(trim($request->request->get('description', '')));
            $service->setCategory(trim($request->request->get('category', '')));

            if ($isNew) {
                $em->persist($service);
            }
            $em->flush();

            $this->addFlash('success', $isNew ? 'Service créé.' : 'Service mis à jour.');
            return $this->redirectToRoute('admin_services_index');
        }

        return $this->render('admin/services/form.html.twig', [
            'service' => $service,
            'isNew'   => $isNew,
        ]);
    }
}
