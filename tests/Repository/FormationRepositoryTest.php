<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $repository;

    /**
     * Cette méthode est exécutée AVANT chaque test.
     * Elle  nettoie la BDD et ajoute les données que nous avons besoin
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Formation::class);

        
        // Je vide la table Formation avant d'ajouter les nouvelles données de test
        // Cela évite d'avoir des doublons si le test précédent a mal nettoyé
        $this->entityManager->createQuery('DELETE FROM App\Entity\Formation')->execute();
        
        // On crée des données de test
        $formation1 = new Formation();
        $formation1->setTitle("Formation Java Avancé");
        $formation1->setPublishedAt(new \DateTime("2024-01-10")); // Plus récent
        $this->entityManager->persist($formation1);

        $formation2 = new Formation();
        $formation2->setTitle("Introduction Python");
        $formation2->setPublishedAt(new \DateTime("2023-12-20")); // Plus ancien
        $this->entityManager->persist($formation2);

        $this->entityManager->flush();
    }

    /**
     * Teste le tri par date (publishedAt)
     */
    public function testFindAllOrderByDate()
    {
        // On récupère les formations triées par date croissante (ASC)
        // La plus ancienne (Python) doit être en premier
        $formations = $this->repository->findAllOrderBy('publishedAt', 'ASC');
        
        // Vérifications
        $this->assertCount(2, $formations); // On doit en avoir 2
        $this->assertEquals("Introduction Python", $formations[0]->getTitle()); // La 1ère doit être Python
    }

    /**
     * Teste le tri par titre
     */
    public function testFindAllOrderByTitle()
    {
        // Tri par titre ASC -> "Formation Java..." (F) avant "Introduction Python" (I)
        $formations = $this->repository->findAllOrderBy('title', 'ASC');
        
        $this->assertEquals("Formation Java Avancé", $formations[0]->getTitle());
    }

    /**
     * Teste la recherche par valeur (filtre)
     */
    public function testFindByContainValue()
    {
        // On cherche "Java"
        $formations = $this->repository->findByContainValue('title', 'Java');

        // On doit en trouver 1 seule
        $this->assertCount(1, $formations);
        $this->assertEquals("Formation Java Avancé", $formations[0]->getTitle());

        // On cherche "C++" (n'existe pas)
        $formations = $this->repository->findByContainValue('title', 'C++');
        $this->assertCount(0, $formations);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}