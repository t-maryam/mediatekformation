<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoriesController extends AbstractController
{
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Page unique : Liste des catégories + Ajout
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request): Response
    {
        // --- GESTION DE L'AJOUT ---
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // Vérification Doublon : est-ce que ce nom existe déjà ?
            $existeDeja = $this->categorieRepository->findOneBy(['name' => $categorie->getName()]);
            
            if($existeDeja){
                $this->addFlash('danger', 'Cette catégorie existe déjà.');
            } else {
                $this->categorieRepository->add($categorie, true);
                $this->addFlash('success', 'Catégorie ajoutée avec succès.');
                // Redirection pour éviter le re-post du formulaire si on fait F5
                return $this->redirectToRoute('admin.categories');
            }
        }

        // --- GESTION DE L'AFFICHAGE ---
        $categories = $this->categorieRepository->findAll();

        return $this->render('admin/admin.categories.html.twig', [
            'categories' => $categories,
            'formcategorie' => $form->createView()
        ]);
    }

    /**
     * Suppression d'une catégorie
     */
    #[Route('/admin/categories/suppr/{id}', name: 'admin.categorie.suppr')]
    public function suppr(int $id, Request $request): Response
    {
        $categorie = $this->categorieRepository->find($id);

        // Contrainte : pas de suppression si rattachée à des formations
        if (count($categorie->getFormations()) > 0) {
            $this->addFlash('danger', 'Impossible de supprimer cette catégorie car elle est utilisée dans des formations.');
            return $this->redirectToRoute('admin.categories');
        }

        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->get('_token'))) {
            $this->categorieRepository->remove($categorie, true);
            $this->addFlash('success', 'La catégorie a été supprimée.');
        }

        return $this->redirectToRoute('admin.categories');
    }
}