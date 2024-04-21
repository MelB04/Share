<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use App\Controller\PostFichierController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(paginationItemsPerPage: 100,operations: [
    new GetCollection(normalizationContext: ['groups' => 'fichier:list']),
    new Post(deserialize: false,
            controller: PostFichierController::class),
    new Get(normalizationContext: ['groups' => 'fichier:item']),
    new Put(),
    new Patch(),
    new Delete(),
],)]

#[ApiFilter(SearchFilter::class, properties: ['proprietaire' => 'exact', 'user'=>'exact'])]
#[ORM\Entity(repositoryClass: FichierRepository::class)]
#[Vich\Uploadable]
class Fichier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fichier:list', 'fichier:item'])]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'files', fileNameProperty: 'nomServeur', size: 'taille')]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fichier:list', 'fichier:item'])]
    private ?string $nomOriginal = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fichier:item'])]
    private ?string $nomServeur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['fichier:item'])]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\Column(length: 3)]
    #[Groups(['fichier:list', 'fichier:item'])]
    private ?string $extension = null;

    #[ORM\Column]
    #[Groups(['fichier:item'])]
    private ?float $taille = null;

    #[ORM\ManyToOne(inversedBy: 'fichiers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['fichier:list', 'fichier:item'])]
    private ?User $proprietaire = null;

    #[ORM\OneToMany(mappedBy: 'fichier', targetEntity: Telecharger::class, orphanRemoval: true)]
    #[Groups(['fichier:item'])]
    private Collection $telechargers;

    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'fichier')]
    #[Groups(['fichier:item'])]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'fichiersPartages')]
    #[Groups(['fichier:item'])]
    private Collection $user;

    public function __construct()
    {
        $this->telechargers = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getNomOriginal(): ?string
    {
        return $this->nomOriginal;
    }

    public function setNomOriginal(string $nomOriginal): static
    {
        $this->nomOriginal = $nomOriginal;

        return $this;
    }

    public function getNomServeur(): ?string
    {
        return $this->nomServeur;
    }

    public function setNomServeur(string $nomServeur): static
    {
        $this->nomServeur = $nomServeur;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getProprietaire(): ?User
    {
        return $this->proprietaire;
    }

    public function getProprietaireId(): ?int
    {
        return $this->proprietaire->getId();
    }

    public function setProprietaire(?User $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * @return Collection<int, Telecharger>
     */
    public function getTelechargers(): Collection
    {
        return $this->telechargers;
    }

    public function addTelecharger(Telecharger $telecharger): static
    {
        if (!$this->telechargers->contains($telecharger)) {
            $this->telechargers->add($telecharger);
            $telecharger->setFichier($this);
        }

        return $this;
    }

    public function removeTelecharger(Telecharger $telecharger): static
    {
        if ($this->telechargers->removeElement($telecharger)) {
            // set the owning side to null (unless already changed)
            if ($telecharger->getFichier() === $this) {
                $telecharger->setFichier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addFichier($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeFichier($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getProprietaireName(): ?string
    {
        return $this->proprietaire->getLastname() ." ". $this->proprietaire->getFirstname();
    }
}

