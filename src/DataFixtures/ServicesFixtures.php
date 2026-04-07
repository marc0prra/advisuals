<?php

// src/DataFixtures/ServiceFixtures.php
namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServicesFixtures extends Fixture
{
    public const SERVICE_PORTRAIT = 'service-portrait';

    public function load(ObjectManager $manager): void
    {
        $services = [
            ['label' => 'Portrait Premium', 'basePrice' => 250.0, 'category' => 'Studio', 'desc' => 'Séance haute définition.'],
            ['label' => 'Shooting Commercial', 'basePrice' => 500.0, 'category' => 'Adidas', 'desc' => 'Campagne publicitaire.'],
            ['label' => 'Reportage Presse', 'basePrice' => 400.0, 'category' => 'Presse', 'desc' => 'Couverture événementielle.']
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