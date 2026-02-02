<?php

namespace App\Tests\Integration;

use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormationValidationsTest extends KernelTestCase
{
    public function testDatePosteriorToTodayIsInvalid(): void
    {
        // 1. Démarrer le noyau Symfony pour accéder aux services
        self::bootKernel();
        $container = static::getContainer();
        
        // 2. Récupérer le validateur
        $validator = $container->get(ValidatorInterface::class);

        // 3. Créer une formation avec une date future (demain)
        $formation = new Formation();
        $formation->setTitle("Formation Futuriste");
        $formation->setPublishedAt(new \DateTime('+1 day')); 

        // 4. Valider l'entité
        $errors = $validator->validate($formation);

        // 5. Vérifier que cela donne bien au moins une erreur
        $this->assertGreaterThan(0, count($errors), "La validation aurait dû échouer pour une date future.");
    }
}