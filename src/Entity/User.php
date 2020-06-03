<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @uniqueEntity(
 * fields = {"email"},
 * message="Un compte est déjà existant à cette adresse Email !!"
 * )
 */

class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractères")
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les mots de passe ne correspondent pas")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe ne correspondent pas")
     */

    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /*
        Pour pouvoir encoder le mot de passe, il faut que notre class (entité) User implémente de la classe UserInterface
        Il faut absolument déclarer les méthodes getRoles(), getSalt(), eraseCredentials(), getUsername(), getPassword()
    */

    // cette méthode est uniquement destinée à nettoyer les mots de passe en texte brut éventuellement stockés
    public function eraseCredentials()
    { 

    }
    // renvoie la chaine de caractère non encodé que l'utilisateur a saisi, qui a été utiliser à l'origine pour coder le mot de passe
    public function getSalt()
    {

    }
    // cette méthode renvoie un tableau de chaine de caractères ou sont stockés les rôles accordées à l'utilisateur
    public function getRoles()
    {
        return ['ROLE_USER'];
    }
}
