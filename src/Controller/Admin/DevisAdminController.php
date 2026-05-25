<?php

namespace App\Controller\Admin;

use App\Entity\RevenueTracking;
use App\Repository\EstimateRequestRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/devis')]
class DevisAdminController extends AbstractController
{
    #[Route('', name: 'app_admin_devis', methods: ['GET'])]
    public function index(EstimateRequestRepository $repo, ServiceRepository $serviceRepo): Response
    {
        $requests = $repo->findBy([], ['created_at' => 'DESC']);

        // Construire un map serviceId → label pour l'affichage
        $services = [];
        foreach ($serviceRepo->findAll() as $service) {
            $services[$service->getId()] = $service->getLabel();
        }

        return $this->render('admin/devis/index.html.twig', [
            'requests' => $requests,
            'services' => $services,
        ]);
    }

    #[Route('/{id}/accept', name: 'app_admin_devis_accept', methods: ['POST'])]
    public function accept(int $id, EstimateRequestRepository $repo, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $request = $repo->find($id);

        if (!$request) {
            $this->addFlash('error', 'Demande introuvable.');
            return $this->redirectToRoute('app_admin_devis');
        }

        if ($request->getStatus() !== 'en_attente') {
            $this->addFlash('error', 'Ce devis a déjà été traité.');
            return $this->redirectToRoute('app_admin_devis');
        }

        $request->setStatus('accepte');

        $total  = $request->getTotalEstimated();
        $adrien = round($total * 0.70, 2);
        $marco  = round($total * 0.30, 2);

        $revenue = new RevenueTracking();
        $revenue->setEstimateId($request->getId());
        $revenue->setAmountTotal($total);
        $revenue->setShareAdrien($adrien);
        $revenue->setShareMarco($marco);
        $revenue->setPaymentDate(new \DateTimeImmutable());

        $em->persist($revenue);
        $em->flush();

        try {
            $mail = (new Email())
                ->from('noreply@advisuals.fr')
                ->to($request->getClientEmail())
                ->subject('Votre demande de devis a été acceptée — AD Visuals')
                ->text(implode("\n\n", [
                    "Bonjour {$request->getClientName()},",
                    "Votre demande de devis d'un montant de {$total} € a été acceptée.",
                    "Nous vous contacterons très prochainement pour la suite.",
                    "Cordialement,\nAdrien Mihajlovic — AD Visuals",
                ]));
            $mailer->send($mail);
        } catch (\Throwable) {}

        $this->addFlash('success', 'Devis accepté. Revenus enregistrés (Adrien : ' . $adrien . ' €).');
        return $this->redirectToRoute('app_admin_devis');
    }

    #[Route('/{id}/refuse', name: 'app_admin_devis_refuse', methods: ['POST'])]
    public function refuse(int $id, EstimateRequestRepository $repo, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $request = $repo->find($id);

        if (!$request) {
            $this->addFlash('error', 'Demande introuvable.');
            return $this->redirectToRoute('app_admin_devis');
        }

        $request->setStatus('refuse');
        $em->flush();

        try {
            $mail = (new Email())
                ->from('noreply@advisuals.fr')
                ->to($request->getClientEmail())
                ->subject('Votre demande de devis — AD Visuals')
                ->text(implode("\n\n", [
                    "Bonjour {$request->getClientName()},",
                    "Après examen de votre demande, nous ne sommes malheureusement pas en mesure d'y donner suite pour le moment.",
                    "N'hésitez pas à nous recontacter pour un autre projet.",
                    "Cordialement,\nAdrien Mihajlovic — AD Visuals",
                ]));
            $mailer->send($mail);
        } catch (\Throwable) {}

        $this->addFlash('success', 'Devis refusé.');
        return $this->redirectToRoute('app_admin_devis');
    }
}
