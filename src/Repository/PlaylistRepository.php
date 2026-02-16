<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository gérant les entités Playlist
 *
 * @extends ServiceEntityRepository<Playlist>
 * @package App\Repository
 */
class PlaylistRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Ajoute une playlist à la base de données
     * @param Playlist $entity
     * @param bool $flush (optionnel) vrai pour valider immédiatement la transaction
     * @return void
     */
    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une playlist de la base de données
     * @param Playlist $entity
     * @param bool $flush (optionnel) vrai pour valider immédiatement la transaction
     * @return void
     */
    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne toutes les playlists triées sur le nom ou le nombre de formations
     * @param string $champ
     * @param string $ordre
     * @return Playlist[]
     */
    public function findAllOrderBy($champ, $ordre): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.formations', 'f')
            ->groupBy('p.id');

        if ($champ == 'nbformations') {
            $qb->orderBy('count(f)', $ordre);
        } else {
            $qb->orderBy('p.' . $champ, $ordre);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Retourne les enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param string $champ
     * @param string $valeur
     * @param string $table (optionnel) si le champ est dans une autre table
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur, $table = ""): array
    {
        if ($valeur == "") {
            // CORRECTION : findAllOrderBy au lieu de findAllOrderByName
            return $this->findAllOrderBy('name', 'ASC');
        }

        if ($table == "") {
            return $this->createQueryBuilder('p')
                ->leftJoin('p.formations', 'f')
                ->where('p.' . $champ . ' LIKE :valeur')
                ->setParameter('valeur', '%' . $valeur . '%')
                ->groupBy('p.id')
                ->orderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('p')
                ->leftJoin('p.formations', 'f')
                ->leftJoin('f.categories', 'c')
                ->where('c.' . $champ . ' LIKE :valeur')
                ->setParameter('valeur', '%' . $valeur . '%')
                ->groupBy('p.id')
                ->orderBy('p.name', 'ASC')
                ->getQuery()
                ->getResult();
        }
    }
}