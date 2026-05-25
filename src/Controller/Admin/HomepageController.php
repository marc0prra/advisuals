<?php

namespace App\Controller\Admin;

use App\Repository\ProjectRepository;
use App\Repository\ServiceRepository;
use App\Repository\SiteSettingsRepository;
use App\Repository\TestimonialRepository;
use App\Entity\Testimonial;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/homepage')]
#[IsGranted('ROLE_ADMIN')]
class HomepageController extends AbstractController
{
    /**
     * Interface principale — grille photos + services + about + témoignages
     */
    #[Route('', name: 'admin_homepage', methods: ['GET'])]
    public function index(
        ProjectRepository $projectRepo,
        ServiceRepository $serviceRepo,
        SiteSettingsRepository $settingsRepo,
        TestimonialRepository $testimonialRepo
    ): Response {
        $allProjects  = $projectRepo->findBy([], ['createdAt' => 'DESC']);
        $featured     = $projectRepo->findBy(['isFeatured' => true], ['featuredPosition' => 'ASC']);
        $allServices  = $serviceRepo->findAll();
        $settings     = $settingsRepo->getOrCreate();
        $testimonials = $testimonialRepo->findAll();

        return $this->render('admin/homepage/index.html.twig', [
            'allProjects'  => $allProjects,
            'featured'     => $featured,
            'allServices'  => $allServices,
            'settings'     => $settings,
            'testimonials' => $testimonials,
        ]);
    }

    /* ── Bloc À propos ── */

    #[Route('/about-block', name: 'admin_homepage_about', methods: ['POST'])]
    public function saveAboutBlock(
        Request $request,
        SiteSettingsRepository $settingsRepo,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $settings = $settingsRepo->getOrCreate();

        $settings->setHomeAboutTitle($request->request->get('title', ''));
        $settings->setHomeAboutText1($request->request->get('text1', ''));
        $settings->setHomeAboutText2($request->request->get('text2', ''));
        $settings->setHomeAboutQuote($request->request->get('quote', ''));

        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            $newFilename = 'about-home-' . uniqid() . '.' . $imageFile->guessExtension();
            try {
                $imageFile->move($uploadsDir, $newFilename);
                $settings->setHomeAboutImagePath('uploads/' . $newFilename);
            } catch (FileException) {
                $this->addFlash('error', 'Erreur upload image.');
            }
        } elseif ($request->request->get('imageUrl')) {
            $settings->setHomeAboutImagePath($request->request->get('imageUrl'));
        }

        $em->flush();
        $this->addFlash('success', 'Bloc À propos mis à jour.');
        return $this->redirectToRoute('admin_homepage', ['#' => 'about-block']);
    }

    /* ── Témoignages ── */

    #[Route('/testimonials', name: 'admin_testimonials', methods: ['GET'])]
    public function testimonials(TestimonialRepository $repo): Response
    {
        return $this->render('admin/testimonials/index.html.twig', [
            'testimonials' => $repo->findBy([], ['featuredPosition' => 'ASC', 'id' => 'ASC']),
        ]);
    }

    #[Route('/testimonials/new', name: 'admin_testimonial_new', methods: ['GET', 'POST'])]
    public function testimonialNew(Request $request, EntityManagerInterface $em, TestimonialRepository $repo): Response
    {
        $testimonial = new Testimonial();
        return $this->handleTestimonialForm($testimonial, $request, $em, $repo, true);
    }

    #[Route('/testimonials/{id}/edit', name: 'admin_testimonial_edit', methods: ['GET', 'POST'])]
    public function testimonialEdit(Testimonial $testimonial, Request $request, EntityManagerInterface $em, TestimonialRepository $repo): Response
    {
        return $this->handleTestimonialForm($testimonial, $request, $em, $repo, false);
    }

    #[Route('/testimonials/{id}/delete', name: 'admin_testimonial_delete', methods: ['POST'])]
    public function testimonialDelete(Testimonial $testimonial, EntityManagerInterface $em): Response
    {
        $em->remove($testimonial);
        $em->flush();
        $this->addFlash('success', 'Témoignage supprimé.');
        return $this->redirectToRoute('admin_testimonials');
    }

    /**
     * AJAX — sauvegarder quels témoignages sont mis en avant (max 3)
     * Body JSON: [id1, id2, id3]
     */
    #[Route('/testimonials/save-featured', name: 'admin_testimonials_save_featured', methods: ['POST'])]
    public function saveTestimonialsFeatured(Request $request, TestimonialRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $ids = json_decode($request->getContent(), true);

        if (!is_array($ids) || count($ids) > 3) {
            return new JsonResponse(['error' => 'Maximum 3 témoignages.'], 400);
        }

        // Reset all
        foreach ($repo->findAll() as $t) {
            $t->setIsFeatured(false);
            $t->setFeaturedPosition(null);
        }

        // Set featured
        foreach ($ids as $position => $id) {
            $t = $repo->find((int) $id);
            if ($t) {
                $t->setIsFeatured(true);
                $t->setFeaturedPosition($position + 1);
            }
        }

        $em->flush();
        return new JsonResponse(['ok' => true]);
    }

    /* ── Services featured (max 3) ── */

    /**
     * AJAX — sauvegarder les services mis en avant (max 3)
     * Body JSON: [id1, id2, id3]
     */
    #[Route('/services/save-featured', name: 'admin_homepage_services_featured', methods: ['POST'])]
    public function saveServicesFeatured(Request $request, ServiceRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $ids = json_decode($request->getContent(), true);

        if (!is_array($ids) || count($ids) > 3) {
            return new JsonResponse(['error' => 'Maximum 3 services.'], 400);
        }

        foreach ($repo->findAll() as $s) {
            $s->setIsFeatured(false);
        }

        foreach ($ids as $id) {
            $s = $repo->find((int) $id);
            if ($s) {
                $s->setIsFeatured(true);
            }
        }

        $em->flush();
        return new JsonResponse(['ok' => true]);
    }

    /* ─── Helpers ─── */

    private function handleTestimonialForm(
        Testimonial $testimonial,
        Request $request,
        EntityManagerInterface $em,
        TestimonialRepository $repo,
        bool $isNew
    ): Response {
        if ($request->isMethod('POST')) {
            $testimonial->setAuthor($request->request->get('author', ''));
            $testimonial->setRole($request->request->get('role', ''));
            $testimonial->setQuote($request->request->get('quote', ''));
            $testimonial->setRating((int) $request->request->get('rating', 5));
            $testimonial->setIsFeatured((bool) $request->request->get('isFeatured', false));

            if ($testimonial->isFeatured()) {
                $pos = (int) $request->request->get('featuredPosition', 1);
                $testimonial->setFeaturedPosition(max(1, min(3, $pos)));
            } else {
                $testimonial->setFeaturedPosition(null);
            }

            if ($isNew) {
                $em->persist($testimonial);
            }
            $em->flush();

            $this->addFlash('success', $isNew ? 'Témoignage créé.' : 'Témoignage mis à jour.');
            return $this->redirectToRoute('admin_testimonials');
        }

        return $this->render('admin/testimonials/edit.html.twig', [
            'testimonial' => $testimonial,
            'isNew'       => $isNew,
        ]);
    }
}
