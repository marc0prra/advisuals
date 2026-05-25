<?php

namespace App\Controller\Admin;

use App\Entity\ApproachStep;
use App\Entity\PracticalInfo;
use App\Entity\Skill;
use App\Repository\ApproachStepRepository;
use App\Repository\PracticalInfoRepository;
use App\Repository\SkillRepository;
use App\Repository\SiteSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/content')]
#[IsGranted('ROLE_ADMIN')]
class ContentController extends AbstractController
{
    /* ═══════════════════════════════════════════
       PAGE ABOUT
    ═══════════════════════════════════════════ */

    #[Route('/about', name: 'admin_content_about', methods: ['GET', 'POST'])]
    public function about(
        Request $request,
        SiteSettingsRepository $settingsRepo,
        SkillRepository $skillRepo,
        ApproachStepRepository $approachRepo,
        EntityManagerInterface $em
    ): Response {
        $settings = $settingsRepo->getOrCreate();

        if ($request->isMethod('POST')) {
            $settings->setAboutBioIntro($request->request->get('bioIntro', ''));
            $settings->setAboutBio1($request->request->get('bio1', ''));
            $settings->setAboutBio2($request->request->get('bio2', ''));
            $settings->setAboutQuote($request->request->get('quote', ''));

            // Portrait — upload ou URL
            $portraitFile = $request->files->get('portraitFile');
            if ($portraitFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                $newFilename = 'portrait-' . uniqid() . '.' . $portraitFile->guessExtension();
                $portraitFile->move($uploadsDir, $newFilename);
                $settings->setAboutPortraitPath('uploads/' . $newFilename);
            } elseif ($request->request->get('portraitUrl')) {
                $settings->setAboutPortraitPath($request->request->get('portraitUrl'));
            }

            $em->flush();
            $this->addFlash('success', 'Contenu À propos mis à jour.');
            return $this->redirectToRoute('admin_content_about');
        }

        return $this->render('admin/content/about.html.twig', [
            'settings'  => $settings,
            'skills'    => $skillRepo->findBy([], ['position' => 'ASC']),
            'steps'     => $approachRepo->findBy([], ['position' => 'ASC']),
        ]);
    }

    /* ── Skills CRUD ── */

    #[Route('/about/skill/new', name: 'admin_skill_new', methods: ['POST'])]
    public function skillNew(Request $request, SkillRepository $repo, EntityManagerInterface $em): Response
    {
        $skill = new Skill();
        $skill->setTitle($request->request->get('title', 'Nouvelle compétence'));
        $skill->setDescription($request->request->get('description', ''));
        $skill->setPosition((int) ($repo->count([]) + 1));
        $em->persist($skill);
        $em->flush();
        $this->addFlash('success', 'Compétence ajoutée.');
        return $this->redirectToRoute('admin_content_about', ['#' => 'skills']);
    }

    #[Route('/about/skill/{id}/edit', name: 'admin_skill_edit', methods: ['POST'])]
    public function skillEdit(Skill $skill, Request $request, EntityManagerInterface $em): Response
    {
        $skill->setTitle($request->request->get('title', $skill->getTitle()));
        $skill->setDescription($request->request->get('description', $skill->getDescription()));
        $em->flush();
        $this->addFlash('success', 'Compétence mise à jour.');
        return $this->redirectToRoute('admin_content_about', ['#' => 'skills']);
    }

    #[Route('/about/skill/{id}/delete', name: 'admin_skill_delete', methods: ['POST'])]
    public function skillDelete(Skill $skill, EntityManagerInterface $em): Response
    {
        $em->remove($skill);
        $em->flush();
        $this->addFlash('success', 'Compétence supprimée.');
        return $this->redirectToRoute('admin_content_about', ['#' => 'skills']);
    }

    /* ── Approach Steps CRUD ── */

    #[Route('/about/step/new', name: 'admin_step_new', methods: ['POST'])]
    public function stepNew(Request $request, ApproachStepRepository $repo, EntityManagerInterface $em): Response
    {
        $step = new ApproachStep();
        $step->setTitle($request->request->get('title', 'Nouvelle étape'));
        $step->setDescription($request->request->get('description', ''));
        $step->setPosition((int) ($repo->count([]) + 1));
        $em->persist($step);
        $em->flush();
        $this->addFlash('success', 'Étape ajoutée.');
        return $this->redirectToRoute('admin_content_about', ['#' => 'approach']);
    }

    #[Route('/about/step/{id}/edit', name: 'admin_step_edit', methods: ['POST'])]
    public function stepEdit(ApproachStep $step, Request $request, EntityManagerInterface $em): Response
    {
        $step->setTitle($request->request->get('title', $step->getTitle()));
        $step->setDescription($request->request->get('description', $step->getDescription()));
        $em->flush();
        $this->addFlash('success', 'Étape mise à jour.');
        return $this->redirectToRoute('admin_content_about', ['#' => 'approach']);
    }

    #[Route('/about/step/{id}/delete', name: 'admin_step_delete', methods: ['POST'])]
    public function stepDelete(ApproachStep $step, EntityManagerInterface $em): Response
    {
        $em->remove($step);
        $em->flush();
        $this->addFlash('success', 'Étape supprimée.');
        return $this->redirectToRoute('admin_content_about', ['#' => 'approach']);
    }

    /* AJAX — réordonnancement drag & drop */
    #[Route('/about/reorder-skills', name: 'admin_reorder_skills', methods: ['POST'])]
    public function reorderSkills(Request $request, SkillRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        return $this->reorderItems($request, $repo, $em, Skill::class);
    }

    #[Route('/about/reorder-steps', name: 'admin_reorder_steps', methods: ['POST'])]
    public function reorderSteps(Request $request, ApproachStepRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        return $this->reorderItems($request, $repo, $em, ApproachStep::class);
    }

    /* ═══════════════════════════════════════════
       INFORMATIONS PRATIQUES (page Services)
    ═══════════════════════════════════════════ */

    #[Route('/practical-info', name: 'admin_practical_info', methods: ['GET', 'POST'])]
    public function practicalInfo(
        Request $request,
        PracticalInfoRepository $repo,
        EntityManagerInterface $em
    ): Response {
        if ($request->isMethod('POST')) {
            $ids   = $request->request->all('ids');
            $labels = $request->request->all('labels');
            $titles = $request->request->all('titles');
            $descs  = $request->request->all('descriptions');

            foreach ($ids as $i => $id) {
                $info = $id ? $repo->find((int) $id) : new PracticalInfo();
                if (!$info) {
                    continue;
                }
                $info->setLabel($labels[$i] ?? '');
                $info->setTitle($titles[$i] ?? '');
                $info->setDescription($descs[$i] ?? '');
                $info->setPosition((int) $i);
                if (!$id) {
                    $em->persist($info);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Informations pratiques mises à jour.');
            return $this->redirectToRoute('admin_practical_info');
        }

        return $this->render('admin/content/practical_info.html.twig', [
            'infos' => $repo->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/practical-info/{id}/delete', name: 'admin_practical_info_delete', methods: ['POST'])]
    public function practicalInfoDelete(PracticalInfo $info, EntityManagerInterface $em): Response
    {
        $em->remove($info);
        $em->flush();
        $this->addFlash('success', 'Information supprimée.');
        return $this->redirectToRoute('admin_practical_info');
    }

    /* ─── helper générique de réordonnancement ── */
    private function reorderItems(Request $request, $repo, EntityManagerInterface $em, string $class): JsonResponse
    {
        $ids = json_decode($request->getContent(), true);
        if (!is_array($ids)) {
            return new JsonResponse(['error' => 'JSON invalide'], 400);
        }
        foreach ($ids as $position => $id) {
            $entity = $repo->find((int) $id);
            if ($entity) {
                $entity->setPosition((int) $position);
            }
        }
        $em->flush();
        return new JsonResponse(['ok' => true]);
    }
}
