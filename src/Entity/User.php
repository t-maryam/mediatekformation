<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité User utilisée pour l'authentification et la gestion de la sécurité
 *
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int|null Identifiant unique de l'utilisateur
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom d'utilisateur (unique)
     */
    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> Liste des rôles de l'utilisateur
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null Mot de passe haché de l'utilisateur
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Retourne l'identifiant de l'utilisateur
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom d'utilisateur
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur
     * @param string $username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Retourne l'identifiant visuel représentant l'utilisateur
     * @see UserInterface
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Retourne les rôles de l'utilisateur
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur
     * @param list<string> $roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe haché
     * @see PasswordAuthenticatedUserInterface
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe haché
     * @param string $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Supprime les données sensibles temporaires
     * @see UserInterface
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}