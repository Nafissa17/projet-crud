<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreSaisons = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $synopsis = null;

    #[ORM\Column(length: 255)]
    private ?string $plateforme = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDiffusion = null;

    #[ORM\Column]
    private ?float $note = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(mimeTypes: ["image/jpeg", "image/png"])]
    private ?string $image = null;


    
    // Relation ManyToMany avec Genre
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'series')]
    #[ORM\JoinTable(name: 'serie_genre')] // Table de jointure
    private Collection $genres;

    // Relation ManyToMany avec Producteur
    #[ORM\ManyToMany(targetEntity: Producteur::class, inversedBy: 'series')]
    #[ORM\JoinTable(name: 'serie_producteur')] // Table de jointure
    private Collection $producteurs;

    public function __construct()
    {
        // Initialisation des collections
        $this->genres = new ArrayCollection();
        $this->producteurs = new ArrayCollection();
    }

    
    public function getImage(): ?string
    {
        return $this->image;
    }

  
    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    
    public function getGenres(): Collection
    {
        return $this->genres;
    }

 
    public function setGenres(Collection $genres): self
    {
        $this->genres = $genres;
        return $this;
    }

   
    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }
        return $this;
    }


    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);
        return $this;
    }

 
    public function getProducteurs(): Collection
    {
        return $this->producteurs;
    }

    
    public function setProducteurs(Collection $producteurs): self
    {
        $this->producteurs = $producteurs;
        return $this;
    }

   
    public function addProducteur(Producteur $producteur): self
    {
        if (!$this->producteurs->contains($producteur)) {
            $this->producteurs->add($producteur);
        }

        return $this;
    }

    
    public function removeProducteur(Producteur $producteur): self
    {
        if ($this->producteurs->contains($producteur)) {
            $this->producteurs->removeElement($producteur);
        }

        return $this;
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNombreSaisons(): ?int
    {
        return $this->nombreSaisons;
    }

    public function setNombreSaisons(?int $nombreSaisons): static
    {
        $this->nombreSaisons = $nombreSaisons;
        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): static
    {
        $this->synopsis = $synopsis;
        return $this;
    }

    public function getPlateforme(): ?string
    {
        return $this->plateforme;
    }

    public function setPlateforme(?string $plateforme): static
    {
        $this->plateforme = $plateforme;
        return $this;
    }

    public function getDateDiffusion(): ?\DateTimeInterface
    {
        return $this->dateDiffusion;
    }

    public function setDateDiffusion(?\DateTimeInterface $dateDiffusion): static
    {
        $this->dateDiffusion = $dateDiffusion;
        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;
        return $this;
    }
}
