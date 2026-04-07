<?php

// src/DataFixtures/ProjectFixtures.php
namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $projects = [
            ['title' => 'Campagne Adidas 2024', 'cat' => 'Commercial', 'img' => 'adidas.jpg', 'featured' => true],
            ['title' => 'Série Portrait Noir & Or', 'cat' => 'Studio', 'img' => 'portrait.jpg', 'featured' => true],
            ['title' => 'Reportage Fashion Week', 'cat' => 'Presse', 'img' => 'fashion.jpg', 'featured' => false]
        ];

        foreach ($projects as $data) {
            $project = new Project();
            $project->setTitle($data['title']);
            $project->setCategory($data['cat']);
            $project->setImagePath($data['img']);
            $project->setIsFeatured($data['featured']);
            $project->setCreatedAt(new \DateTimeImmutable());
            
            $manager->persist($project);
        }

        $manager->flush();
    }
}