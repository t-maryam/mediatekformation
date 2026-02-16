<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gestion de la page d'accueil et des pages d'informations générales
 *
 * @package App\Controller
 */
class AccueilController extends AbstractController
{

    /**
     * @var FormationRepository
     */
    private $repository;

    /**
     * Constructeur du contrôleur pour injecter le repository
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Affiche les dernières formations sur la page d'accueil
     * @return Response
     */
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/accueil.html.twig", [
                    'formations' => $formations
        ]);
    }

    /**
     * Affiche la page des conditions générales d'utilisation (CGU)
     * @return Response
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}