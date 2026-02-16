<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une playlist regroupant plusieurs formations
 *
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{

    /**
     * @var int|null Identifiant unique de la playlist
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom de la playlist
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    /**
     * @var string|null Description de la playlist
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Formation> Collection des formations rattachées à cette playlist
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'playlist')]
    private Collection $formations;

    /**
     * Constructeur : initialise la collection de formations
     */
    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la playlist
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de la playlist
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la playlist
     * @param string|null $name
     * @return static
     */
    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne la description de la playlist
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la playlist
     * @param string|null $description
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne la collection des formations de la playlist
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    /**
     * Ajoute une formation à la playlist (gestion de la relation bidirectionnelle)
     * @param Formation $formation
     * @return static
     */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }

        return $this;
    }

    /**
     * Retire une formation de la playlist (gestion de la relation bidirectionnelle)
     * @param Formation $formation
     * @return static
     */
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation) && $formation->getPlaylist() === $this) {
            $formation->setPlaylist(null);
        }
        return $this;
    }

    /**
     * Récupère la liste unique des noms de catégories présentes dans la playlist
     * @return Collection<int, string>
     */
    public function getCategoriesPlaylist(): Collection
    {
        $categories = new ArrayCollection();
        foreach ($this->formations as $formation) {
            $categoriesFormation = $formation->getCategories();
            foreach ($categoriesFormation as $categorieFormation) {
                if (!$categories->contains($categorieFormation->getName())) {
                    $categories[] = $categorieFormation->getName();
                }
            }
        }
        return $categories;
    }
}