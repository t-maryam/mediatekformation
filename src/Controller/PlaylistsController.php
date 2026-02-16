<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant l'affichage des playlists côté public
 *
 * @package App\Controller
 */
class PlaylistsController extends AbstractController 
{

    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;

    /**
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Chemin vers le template de la page des playlists
     */
    private const PAGE_PLAYLISTS = "pages/playlists.html.twig";

    /**
     * Constructeur du contrôleur pour injecter les repositories
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRepository
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }

    /**
     * Affiche la liste complète des playlists
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response 
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name','ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    /**
     * Gère le tri des playlists
     * @param string $champ
     * @param string $ordre
     * @return Response
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response 
    {
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderBy($champ,$ordre);
        } else {
            $playlists = $this->playlistRepository->findAll();
        }

        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    /**
     * Gère la recherche filtrée des playlists
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response 
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
                    'playlists' => $playlists,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    /**
     * Affiche le détail d'une playlist spécifique
     * @param int $id
     * @return Response
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response 
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render('pages/playlist.html.twig', [
                    'playlist' => $playlist,
                    'playlistcategories' => $playlistCategories,
                    'playlistformations' => $playlistFormations
        ]);
    }
}