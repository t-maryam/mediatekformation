<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository gérant les entités Categorie
 * * @extends ServiceEntityRepository<Categorie>
 * @package App\Repository
 */
class CategorieRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /**
     * Ajoute une catégorie à la base de données
     * @param Categorie $entity
     * @param bool $flush (optionnel) vrai pour valider immédiatement la transaction
     * @return void
     */
    public function add(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une catégorie de la base de données
     * @param Categorie $entity
     * @param bool $flush (optionnel) vrai pour valider immédiatement la transaction
     * @return void
     */
    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère la liste des catégories liées à une playlist spécifique
     * @param int $idPlaylist identifiant de la playlist
     * @return Categorie[]
     */
    public function findAllForOnePlaylist($idPlaylist): array
    {
        return $this->createQueryBuilder('c')
                        ->join('c.formations', 'f')
                        ->join('f.playlist', 'p')
                        ->where('p.id=:id')
                        ->setParameter('id', $idPlaylist)
                        ->orderBy('c.name', 'ASC')
                        ->getQuery()
                        ->getResult();
    }
}