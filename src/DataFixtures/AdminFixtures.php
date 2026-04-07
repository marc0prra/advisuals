<?php

// src/DataFixtures/AdminFixtures.php
namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admins = [
            ['email' => 'adrienmihajlovic.pro@gmail.com', 'role' => 'ROLE_ADMIN'],
            ['email' => 'marco@example.com', 'role' => 'ROLE_ADMIN']
        ];

        foreach ($admins as $data) {
            $admin = new Admin();
            $admin->setEmail($data['email']);
            $admin->setRoles([$data['role']]);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password123'));
            $manager->persist($admin);
        }

        $manager->flush();
    }
}