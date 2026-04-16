<?php

namespace App\DataFixtures;

use App\Entity\EstimateRequest;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EstimateRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $requests = [
            [
                'name'    => 'Sophie Marceau',
                'email'   => 'sophie.m@agenceparis.fr',
                'phone'   => '0611223344',
                'svc'     => 0,
                'total'   => 400.0,
                'msg'     => 'Besoin d\'un portrait professionnel pour ma nouvelle carte de visite et mon LinkedIn. Je souhaite un style épuré et moderne.',
                'status'  => 'accepte',
                'date'    => '-8 days',
            ],
            [
                'name'    => 'Thomas Renault — Agence R&Co',
                'email'   => 'thomas@agence-rco.com',
                'phone'   => '0699887766',
                'svc'     => 1,
                'total'   => 1250.0,
                'msg'     => 'Campagne printemps-été pour notre client dans le secteur sportswear. Besoin de 3 journées de shooting avec direction artistique complète.',
                'status'  => 'accepte',
                'date'    => '-5 days',
            ],
            [
                'name'    => 'Clara Fontaine',
                'email'   => 'cfontaine@vogueparis.fr',
                'phone'   => '0655443322',
                'svc'     => 2,
                'total'   => 640.0,
                'msg'     => 'Reportage backstage pour la prochaine fashion week. Accès presse confirmé, besoin d\'un photographe réactif et discret.',
                'status'  => 'en_attente',
                'date'    => '-2 days',
            ],
            [
                'name'    => 'Marc Bellini',
                'email'   => 'marc.bellini@studio-mb.com',
                'phone'   => '0644556677',
                'svc'     => 3,
                'total'   => 2000.0,
                'msg'     => 'Refonte complète de l\'identité visuelle de notre marque de prêt-à-porter. Moodboard, casting et direction artistique sur 2 jours.',
                'status'  => 'en_attente',
                'date'    => '-1 days',
            ],
            [
                'name'    => 'Julie Deschamps',
                'email'   => 'julie.d@gmail.com',
                'phone'   => '0612345678',
                'svc'     => 0,
                'total'   => 250.0,
                'msg'     => 'Portrait artistique pour offrir en cadeau à mon copain pour son anniversaire. Ambiance douce et lumineuse.',
                'status'  => 'refuse',
                'date'    => '-15 days',
            ],
            [
                'name'    => 'Kenji Watanabe — Lacoste',
                'email'   => 'kwatanabe@lacoste.com',
                'phone'   => '0677889900',
                'svc'     => 1,
                'total'   => 800.0,
                'msg'     => 'Lookbook été 2025, 4 modèles, 2 décors. Budget confirmé, planning flexible.',
                'status'  => 'en_attente',
                'date'    => '-4 hours',
            ],
        ];

        foreach ($requests as $data) {
            /** @var Service $service */
            $service = $this->getReference('service_' . $data['svc'], Service::class);

            $req = new EstimateRequest();
            $req->setClientName($data['name']);
            $req->setClientEmail($data['email']);
            $req->setClientPhone($data['phone']);
            $req->setServiceId($service->getId());
            $req->setTotalEstimated($data['total']);
            $req->setMessage($data['msg']);
            $req->setStatus($data['status']);
            $req->setCreatedAt(new \DateTimeImmutable($data['date']));

            $manager->persist($req);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ServicesFixtures::class];
    }
}
