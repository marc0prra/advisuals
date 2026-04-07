<?php

// src/DataFixtures/EstimateRequestFixtures.php
namespace App\DataFixtures;

use App\Entity\EstimateRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EstimateRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $request = new EstimateRequest();
        $request->setClientName('Jean Dupont');
        $request->setClientEmail('jean.dupont@example.com');
        $request->setClientPhone('0601020304');
        $request->setTotalEstimated(750.0);
        $request->setMessage('Demande de devis pour un shooting studio de 4h.');
        $request->setStatus('en_attente');
        $request->setCreatedAt(new \DateTimeImmutable());
        
        // Relation avec un service via référence
        $request->setService($this->getReference('service_0'));

        $manager->persist($request);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ServiceFixtures::class];
    }
}