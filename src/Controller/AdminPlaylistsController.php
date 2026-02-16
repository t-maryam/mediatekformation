<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant la partie administration des playlists
 *
 * @package App\Controller
 */
class AdminPlaylistsController extends AbstractController
{
    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Constructeur du contrôleur pour injecter les repositories
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(PlaylistRepository $playlistRepository, CategorieRepository $categorieRepository)
    {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste des playlists pour l'administration
     * @return Response
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name', 'ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.playlists.html.twig', [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * Gère le tri des playlists dans la liste administrative
     * @param string $champ
     * @param string $ordre
     * @return Response
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.playlists.html.twig', [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * Gère la recherche filtrée des playlists pour l'administration
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.playlists.html.twig', [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * Supprime une playlist si elle ne contient aucune formation associée
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/playlists/suppr/{id}', name: 'admin.playlist.suppr')]
    public function suppr(int $id, Request $request): Response
    {
        $playlist = $this->playlistRepository->find($id);

        // Contrainte : suppression impossible s'il y a des formations
        if (count($playlist->getFormations()) > 0) {
            $this->addFlash('danger', 'Impossible de supprimer la playlist car elle contient des formations.');
            return $this->redirectToRoute('admin.playlists');
        }

        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->get('_token'))) {
            $this->playlistRepository->remove($playlist, true);
            $this->addFlash('success', 'La playlist a été supprimée avec succès');
        }

        return $this->redirectToRoute('admin.playlists');
    }

    /**
     * Gère le formulaire d'ajout d'une nouvelle playlist
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/playlists/ajout', name: 'admin.playlist.ajout')]
    public function ajout(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render('admin/admin.playlist.edit.html.twig', [
            'formplaylist' => $form->createView(),
            'playlist' => $playlist
        ]);
    }

    /**
     * Gère le formulaire de modification d'une playlist existante
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/playlists/edit/{id}', name: 'admin.playlist.edit')]
    public function edit(int $id, Request $request): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $form = $this->createForm(PlaylistType::class, $playlist);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render('admin/admin.playlist.edit.html.twig', [
            'formplaylist' => $form->createView(),
            'playlist' => $playlist
        ]);
    }
}