<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/admins')]
class AdminManagementController extends AbstractController
{
    #[Route('', name: 'app_admin_admins', methods: ['GET'])]
    public function index(AdminRepository $repo): Response
    {
        return $this->render('admin/admins/index.html.twig', [
            'admins' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_admins_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $email    = trim($request->request->get('email', ''));
            $password = trim($request->request->get('password', ''));
            $role     = $request->request->get('role', 'ROLE_ADMIN');

            if ($email && $password) {
                $admin = new Admin();
                $admin->setEmail($email);
                $admin->setRoles([$role]);
                $admin->setPassword($hasher->hashPassword($admin, $password));

                $em->persist($admin);
                $em->flush();

                $this->addFlash('success', 'Admin créé avec succès.');
                return $this->redirectToRoute('app_admin_admins');
            }

            $this->addFlash('error', 'Email et mot de passe requis.');
        }

        return $this->render('admin/admins/form.html.twig', [
            'admin'  => null,
            'action' => $this->generateUrl('app_admin_admins_new'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_admins_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, AdminRepository $repo, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $admin = $repo->find($id);

        if (!$admin) {
            $this->addFlash('error', 'Admin introuvable.');
            return $this->redirectToRoute('app_admin_admins');
        }

        if ($request->isMethod('POST')) {
            $email    = trim($request->request->get('email', ''));
            $role     = $request->request->get('role', 'ROLE_ADMIN');
            $password = trim($request->request->get('password', ''));

            if ($email) {
                $admin->setEmail($email);
                $admin->setRoles([$role]);

                if ($password) {
                    $admin->setPassword($hasher->hashPassword($admin, $password));
                }

                $em->flush();
                $this->addFlash('success', 'Admin modifié avec succès.');
                return $this->redirectToRoute('app_admin_admins');
            }

            $this->addFlash('error', 'Email requis.');
        }

        return $this->render('admin/admins/form.html.twig', [
            'admin'  => $admin,
            'action' => $this->generateUrl('app_admin_admins_edit', ['id' => $id]),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_admins_delete', methods: ['POST'])]
    public function delete(int $id, AdminRepository $repo, EntityManagerInterface $em): Response
    {
        if ($repo->count([]) <= 1) {
            $this->addFlash('error', 'Impossible de supprimer le dernier administrateur.');
            return $this->redirectToRoute('app_admin_admins');
        }

        $admin = $repo->find($id);

        if ($admin) {
            $em->remove($admin);
            $em->flush();
            $this->addFlash('success', 'Admin supprimé.');
        }

        return $this->redirectToRoute('app_admin_admins');
    }
}
