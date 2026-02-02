<?php

namespace App\Tests;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtString(): void
    {
        // 1. On instancie l'entité Formation
        $formation = new Formation();

        // 2. On crée une date avec une heure (ex: 4 Janvier 2024 à 17h)
        $date = new \DateTime("2024-01-04 17:00:12");
        $formation->setPublishedAt($date);

        // 3. On vérifie que la méthode renvoie bien "04/01/2024" (d/m/Y)
        $this->assertEquals("04/01/2024", $formation->getPublishedAtString());
    }
}