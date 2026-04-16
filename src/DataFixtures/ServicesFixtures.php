<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServicesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $services = [
            [
                'label'     => 'Portrait & Studio',
                'basePrice' => 250.0,
                'category'  => 'Studio',
                'desc'      => 'Séances en studio haute définition — portrait professionnel, artistique ou éditorial. Lumière maîtrisée, retouche incluse.',
            ],
            [
                'label'     => 'Shooting Commercial',
                'basePrice' => 500.0,
                'category'  => 'Commercial',
                'desc'      => 'Campagnes publicitaires, lookbooks, catalogues produits. Direction artistique complète adaptée à votre identité de marque.',
            ],
            [
                'label'     => 'Reportage & Presse',
                'basePrice' => 400.0,
                'category'  => 'Presse',
                'desc'      => 'Couvertures d\'événements, fashion weeks, backstages. Livraison rapide pour vos besoins éditoriaux et rédactionnels.',
            ],
            [
                'label'     => 'Direction Artistique',
                'basePrice' => 800.0,
                'category'  => 'Commercial',
                'desc'      => 'Conception globale de projet visuel — moodboard, casting, direction de shooting, post-production. Pour les projets exigeants.',
            ],
        ];

        foreach ($services as $key => $data) {
            $service = new Service();
            $service->setLabel($data['label']);
            $service->setBasePrice($data['basePrice']);
            $service->setCategory($data['category']);
            $service->setDescription($data['desc']);

            $manager->persist($service);
            $this->addReference('service_' . $key, $service);
        }

        $manager->flush();
    }
}
