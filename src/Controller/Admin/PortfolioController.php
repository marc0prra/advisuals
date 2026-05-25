<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/portfolio')]
#[IsGranted('ROLE_ADMIN')]
class PortfolioController extends AbstractController
{
    #[Route('', name: 'admin_portfolio_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('admin/portfolio/index.html.twig', [
            'projects' => $projectRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_portfolio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $project = new Project();
        return $this->handleForm($project, $request, $em, $slugger, true);
    }

    #[Route('/{id}/edit', name: 'admin_portfolio_edit', methods: ['GET', 'POST'])]
    public function edit(Project $project, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        return $this->handleForm($project, $request, $em, $slugger, false);
    }

    #[Route('/{id}/delete', name: 'admin_portfolio_delete', methods: ['POST'])]
    public function delete(Project $project, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_project_' . $project->getId(), $request->request->get('_token'))) {
            $em->remove($project);
            $em->flush();
            $this->addFlash('success', 'Projet supprimé.');
        }
        return $this->redirectToRoute('admin_portfolio_index');
    }

    /**
     * AJAX — upload d'une image par drag & drop sur une carte
     * POST /admin/portfolio/{id}/upload-image
     */
    #[Route('/{id}/upload-image', name: 'admin_portfolio_upload_image', methods: ['POST'])]
    public function uploadImage(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): JsonResponse {
        $file = $request->files->get('image');

        if (!$file) {
            return new JsonResponse(['error' => 'Aucun fichier reçu.'], 400);
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            return new JsonResponse(['error' => 'Format non supporté.'], 400);
        }

        $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/projects';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $safeFilename = $slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $newFilename  = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($uploadsDir, $newFilename);
        } catch (FileException) {
            return new JsonResponse(['error' => 'Erreur lors du déplacement du fichier.'], 500);
        }

        $project->setImagePath('uploads/projects/' . $newFilename);
        $em->flush();

        return new JsonResponse(['path' => '/uploads/projects/' . $newFilename]);
    }

    /**
     * AJAX — sauvegarde ordre + taille des photos featured
     * POST /admin/portfolio/save-featured-grid
     * Body JSON: [{ id, position, size }]
     */
    #[Route('/save-featured-grid', name: 'admin_portfolio_save_featured_grid', methods: ['POST'])]
    public function saveFeaturedGrid(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'JSON invalide.'], 400);
        }

        // Réinitialise tous les projets featured
        foreach ($projectRepository->findBy(['isFeatured' => true]) as $p) {
            $p->setIsFeatured(false);
            $p->setFeaturedPosition(null);
        }

        foreach ($data as $item) {
            $project = $projectRepository->find($item['id'] ?? 0);
            if (!$project) {
                continue;
            }
            $project->setIsFeatured(true);
            $project->setFeaturedPosition((int) ($item['position'] ?? 0));
            $project->setFeaturedSize(in_array($item['size'] ?? '', ['normal', 'wide'], true) ? $item['size'] : 'normal');
        }

        $em->flush();

        return new JsonResponse(['ok' => true]);
    }

    /* ────────────────────────────────────────── */

    private function handleForm(
        Project $project,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        bool $isNew
    ): Response {
        if ($request->isMethod('POST')) {
            $project->setTitle($request->request->get('title', ''));
            $project->setCategory($request->request->get('category', ''));
            $project->setDescription($request->request->get('description'));
            $project->setIsFeatured((bool) $request->request->get('isFeatured', false));

            // Gestion upload image
            $imageFile = $request->files->get('imageFile');
            if ($imageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/projects';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                $safeFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
                $newFilename  = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $project->setImagePath('uploads/projects/' . $newFilename);
                } catch (FileException) {
                    $this->addFlash('error', 'Erreur upload image.');
                }
            } elseif ($request->request->get('imageUrl')) {
                $project->setImagePath($request->request->get('imageUrl'));
            }

            if ($isNew) {
                $em->persist($project);
            }
            $em->flush();

            $this->addFlash('success', $isNew ? 'Projet créé.' : 'Projet mis à jour.');
            return $this->redirectToRoute('admin_portfolio_index');
        }

        return $this->render('admin/portfolio/edit.html.twig', [
            'project' => $project,
            'isNew'   => $isNew,
            'categories' => ['Studio', 'Commercial', 'Presse', 'Portrait', 'Événementiel', 'Autre'],
        ]);
    }
}
