<?php

namespace App\Entity;

use App\Repository\ReponseReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseReclamationRepository::class)]
class ReponseReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le contenu de la réponse est obligatoire.')]
    #[Assert\Length(min: 10, minMessage: 'La réponse doit faire au moins {{ limit }} caractères.')]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'La réclamation concernée est obligatoire.')]
    private ?Reclamation $reclamation = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: 'Le type d\'auteur ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $auteurType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'L\'avis ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $avisUtilisateur = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 120, minMessage: 'Le nom doit faire au moins {{ limit }} caractères.', maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    public function getAuteurType(): ?string
    {
        return $this->auteurType;
    }

    public function setAuteurType(?string $auteurType): static
    {
        $this->auteurType = $auteurType;
        return $this;
    }

    public function getAvisUtilisateur(): ?string
    {
        return $this->avisUtilisateur;
    }

    public function setAvisUtilisateur(?string $avisUtilisateur): static
    {
        $this->avisUtilisateur = $avisUtilisateur;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }
}
