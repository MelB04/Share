<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

#[ApiResource()]
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: Fichier::class, inversedBy: 'categories')]
    private Collection $fichier;

    public function __construct()
    {
        $this->fichier = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichier(): Collection
    {
        return $this->fichier;
    }

    public function addFichier(Fichier $fichier): static
    {
        if (!$this->fichier->contains($fichier)) {
            $this->fichier->add($fichier);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): static
    {
        $this->fichier->removeElement($fichier);

        return $this;
    }

    public function getJSONCategorie()
    {
        return json_encode([
            "libelle" => $this->getLibelle(),
            "id" => $this->getId(),
        ]);
    }
}
