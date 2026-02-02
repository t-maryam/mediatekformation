<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
{
    private $entityManager;
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Playlist::class);

        // Nettoyage de la BDD, on reprends de zéro
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
       
        // 1. Création de la Playlist A (Java)
        $playlistJava = new Playlist();
        $playlistJava->setName("Java Base");
        $this->entityManager->persist($playlistJava);

        // Ajout de 2 formations
        $f1 = new Formation();
        $f1->setTitle("Java 1");
        $f1->setPlaylist($playlistJava);
        $this->entityManager->persist($f1);

        $f2 = new Formation();
        $f2->setTitle("Java 2");
        $f2->setPlaylist($playlistJava);
        $this->entityManager->persist($f2);

        // 2. Création de la Playlist B (Python) - Vide
        $playlistPython = new Playlist();
        $playlistPython->setName("Python Avancé");
        $this->entityManager->persist($playlistPython);

        $this->entityManager->flush();
    }

    public function testFindAllOrderByName()
    {
        $playlists = $this->repository->findAllOrderBy('name', 'ASC');
        $this->assertCount(2, $playlists);
        $this->assertEquals("Java Base", $playlists[0]->getName());

        $playlists = $this->repository->findAllOrderBy('name', 'DESC');
        $this->assertEquals("Python Avancé", $playlists[0]->getName());
    }

    public function testFindAllOrderByNbFormations()
    {
        $playlists = $this->repository->findAllOrderBy('nbformations', 'ASC');
        $this->assertEquals("Python Avancé", $playlists[0]->getName());

        $playlists = $this->repository->findAllOrderBy('nbformations', 'DESC');
        $this->assertEquals("Java Base", $playlists[0]->getName());
    }

    public function testFindByContainValue()
    {
        $playlists = $this->repository->findByContainValue('name', 'Python');
        $this->assertCount(1, $playlists);
        $this->assertEquals("Python Avancé", $playlists[0]->getName());

        $playlists = $this->repository->findByContainValue('name', 'C#');
        $this->assertCount(0, $playlists);
    }
}