<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $projects = [
            // Studio
            [
                'title'    => 'Série Noir & Or — Portrait',
                'cat'      => 'Studio',
                'img'      => 'https://picsum.photos/seed/portrait-noir/600/800',
                'featured' => true,
                'date'     => '-2 days',
            ],
            [
                'title'    => 'Direction Artistique — Beauté',
                'cat'      => 'Studio',
                'img'      => 'https://picsum.photos/seed/beauty-studio/600/800',
                'featured' => true,
                'date'     => '-5 days',
            ],
            [
                'title'    => 'Portrait Corporate — CEO',
                'cat'      => 'Studio',
                'img'      => 'https://picsum.photos/seed/corporate-ceo/600/800',
                'featured' => false,
                'date'     => '-10 days',
            ],
            [
                'title'    => 'Éditorial Mode — Collection Hiver',
                'cat'      => 'Studio',
                'img'      => 'https://picsum.photos/seed/mode-hiver/600/800',
                'featured' => false,
                'date'     => '-15 days',
            ],
            // Commercial
            [
                'title'    => 'Campagne Adidas — SS24',
                'cat'      => 'Commercial',
                'img'      => 'https://picsum.photos/seed/adidas-ss24/600/800',
                'featured' => true,
                'date'     => '-3 days',
            ],
            [
                'title'    => 'Lookbook Lacoste — Été 2024',
                'cat'      => 'Commercial',
                'img'      => 'https://picsum.photos/seed/lacoste-ete/600/800',
                'featured' => true,
                'date'     => '-7 days',
            ],
            [
                'title'    => 'Catalogue Produit — Maison Martin',
                'cat'      => 'Commercial',
                'img'      => 'https://picsum.photos/seed/catalogue-martin/600/800',
                'featured' => false,
                'date'     => '-20 days',
            ],
            // Presse
            [
                'title'    => 'Fashion Week Paris — Backstage',
                'cat'      => 'Presse',
                'img'      => 'https://picsum.photos/seed/fashion-week/600/800',
                'featured' => true,
                'date'     => '-1 days',
            ],
            [
                'title'    => 'Reportage — Semaine de la Mode',
                'cat'      => 'Presse',
                'img'      => 'https://picsum.photos/seed/mode-semaine/600/800',
                'featured' => false,
                'date'     => '-12 days',
            ],
            [
                'title'    => 'Couverture Vogue — Hors Série',
                'cat'      => 'Presse',
                'img'      => 'https://picsum.photos/seed/vogue-cover/600/800',
                'featured' => false,
                'date'     => '-25 days',
            ],
        ];

        foreach ($projects as $data) {
            $project = new Project();
            $project->setTitle($data['title']);
            $project->setCategory($data['cat']);
            $project->setImagePath($data['img']);
            $project->setIsFeatured($data['featured']);
            $project->setCreatedAt(new \DateTimeImmutable($data['date']));
            $manager->persist($project);
        }

        $manager->flush();
    }
}
