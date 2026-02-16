<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant l'affichage des formations côté public
 *
 * @package App\Controller
 */
class FormationsController extends AbstractController
{

    /**
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    /**
     * Chemin vers le template de la page des formations
     */
    private const PAGE_FORMATIONS = "pages/formations.html.twig";

    /**
     * Constructeur du contrôleur pour injecter les repositories
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository
    ) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste complète des formations
     * @return Response
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * Gère le tri des formations selon le champ et l'ordre demandés
     * @param string $champ
     * @param string $ordre
     * @param string $table
     * @return Response
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table = ""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * Gère la recherche filtrée des formations
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
                    'formations' => $formations,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    /**
     * Affiche le détail d'une formation spécifique
     * @param int $id
     * @return Response
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render('pages/formation.html.twig', [
                    'formation' => $formation
        ]);
    }
}