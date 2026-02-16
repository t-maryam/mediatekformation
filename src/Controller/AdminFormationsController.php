<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Form\FormationType;

/**
 * Contrôleur gérant la partie administration des formations
 *

 * @package App\Controller
 */
class AdminFormationsController extends AbstractController
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
     * Constructeur du contrôleur pour injecter les repositories
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste des formations pour l'administration
     * @return Response
     */
    #[Route('/admin', name: 'admin.formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Gère le tri des formations dans la liste administrative
     * @param string $champ
     * @param string $ordre
     * @param string $table
     * @return Response
     */
    #[Route('/admin/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * Gère la recherche filtrée des formations pour l'administration
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/admin/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Supprime une formation via son identifiant
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/suppr/{id}', name: 'admin.formation.suppr')]
    public function suppr(int $id, Request $request): Response
    {
        $formation = $this->formationRepository->find($id);

        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->get('_token'))) {
            $this->formationRepository->remove($formation);
            $this->addFlash('success', 'La formation a été supprimée avec succès');
        }
        
        return $this->redirectToRoute('admin.formations');
    }
    
    /**
     * Gère le formulaire d'ajout d'une nouvelle formation
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/ajout', name: 'admin.formation.ajout')]
    public function ajout(Request $request): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render('admin/admin.formation.edit.html.twig', [
            'formformation' => $form->createView(),
            'formation' => $formation 
        ]);
    }

    /**
     * Gère le formulaire de modification d'une formation existante
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/edit/{id}', name: 'admin.formation.edit')]
    public function edit(int $id, Request $request): Response
    {
        $formation = $this->formationRepository->find($id);
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render('admin/admin.formation.edit.html.twig', [
            'formformation' => $form->createView(),
            'formation' => $formation
        ]);
    }
}