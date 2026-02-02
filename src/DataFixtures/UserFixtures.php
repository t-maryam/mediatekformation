<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de l'administrateur
        $user = new User();
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']); // On lui donne le droit ADMIN

        // Hashage du mot de passe "admin" (tu pourras le changer)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'admin'
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}