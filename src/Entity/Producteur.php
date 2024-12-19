<?php

namespace App\Entity;

use App\Repository\ProducteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ProducteurRepository::class)]
class Producteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    // Relation ManyToMany avec Serie
    #[ORM\ManyToMany(targetEntity: Serie::class, mappedBy: 'producteurs')]
    private Collection $series;

    public function __construct()
    {
        $this->series = new ArrayCollection();
    }

    
    public function getSeries(): Collection
    {
        return $this->series;
    }

    
    public function getId(): ?int
    {
        return $this->id;
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
