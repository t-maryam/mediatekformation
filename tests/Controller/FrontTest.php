<?php

namespace App\Tests\Controller;

use App\Entity\Formation;
use App\Entity\Playlist; 
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FrontTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        // 1. Création du client
        $this->client = static::createClient();
        
        // 2. Récupération de l'EntityManager
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        // 3. Reset de la BDD
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);

        // 4. Ajout des fausses données
        
        // A. On crée d'abord une Playlist pour éviter l'erreur "name on null"
        $playlist = new Playlist();
        $playlist->setName("Ma super playlist");
        $this->entityManager->persist($playlist);

        // B. On crée les formations en les liant à la playlist
        $f1 = new Formation();
        $f1->setTitle("Formation A (Java)");
        $f1->setVideoId("1234");
        $f1->setPlaylist($playlist); 
        $this->entityManager->persist($f1);

        $f2 = new Formation();
        $f2->setTitle("Formation B (Python)");
        $f2->setVideoId("5678");
        $f2->setPlaylist($playlist); 
        $this->entityManager->persist($f2);

        $this->entityManager->flush();
    }

    public function testAccesAccueil()
    {
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testTriFormations()
    {
        $crawler = $this->client->request('GET', '/formations');
        $crawler = $this->client->request('GET', '/formations/tri/title/DESC');

        $premierTitre = $crawler->filter('h5')->eq(0)->text();
        $this->assertStringContainsString("Formation B (Python)", $premierTitre);
    }

    public function testFiltreFormations()
    {
        $crawler = $this->client->request('GET', '/formations');
        $buttonCrawlerNode = $crawler->selectButton('filtrer');
        $form = $buttonCrawlerNode->form();
        $form['recherche'] = 'Python';
        
        $crawler = $this->client->submit($form);

        $nbResultats = $crawler->filter('h5')->count(); 
        $this->assertEquals(1, $nbResultats);

        $premierTitre = $crawler->filter('h5')->eq(0)->text();
        $this->assertStringContainsString("Formation B (Python)", $premierTitre);
    }

    public function testLinkNavigation()
    {
        $crawler = $this->client->request('GET', '/formations');
        
        // On cherche le lien via l'attribut ALT de l'image
        $link = $crawler->selectLink('Ilustration de la formation')->link();
        
        $crawler = $this->client->click($link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        // On vérifie que le détail contient bien le titre
        $this->assertSelectorTextContains('body', 'Formation A (Java)');
    }
}