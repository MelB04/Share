<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Json;
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

#[ApiResource(paginationItemsPerPage: 10, operations: [
    new GetCollection(normalizationContext: ['groups' => 'user:list']),
    new Post(),
    new Get(normalizationContext: ['groups' => 'user:item']),
    new Put(),
    new Patch(),
    new Delete(),
    ])]

#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'lastname' => 'exact', 'firstname' => 'partial'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:list', 'user:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:list', 'user:item'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:item'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateRegister = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:list', 'user:item','message:list', 'message:item'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:list', 'user:item','message:list', 'message:item'])]
    private ?string $firstname = null;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Fichier::class, orphanRemoval: true)]
    private Collection $fichiers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Telecharger::class, orphanRemoval: true)]
    private Collection $telechargers;

    #[ORM\ManyToMany(targetEntity: Fichier::class, mappedBy: 'user')]
    private Collection $fichiersPartages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->fichiers = new ArrayCollection();
        $this->telechargers = new ArrayCollection();
        $this->fichiersPartages = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getDateRegister(): ?\DateTimeInterface
    {
        return $this->dateRegister;
    }

    public function setDateRegister(\DateTimeInterface $dateRegister): static
    {
        $this->dateRegister = $dateRegister;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichier $fichier): static
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers->add($fichier);
            $fichier->setProprietaire($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): static
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getProprietaire() === $this) {
                $fichier->setProprietaire(null);
            }
        }

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
            $telecharger->setUser($this);
        }

        return $this;
    }

    public function removeTelecharger(Telecharger $telecharger): static
    {
        if ($this->telechargers->removeElement($telecharger)) {
            // set the owning side to null (unless already changed)
            if ($telecharger->getUser() === $this) {
                $telecharger->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fichier>
     */
    public function getFichiersPartages(): Collection
    {
        return $this->fichiersPartages;
    }

    public function addFichiersPartage(Fichier $fichiersPartage): static
    {
        if (!$this->fichiersPartages->contains($fichiersPartage)) {
            $this->fichiersPartages->add($fichiersPartage);
            $fichiersPartage->addUser($this);
        }

        return $this;
    }

    public function removeFichiersPartage(Fichier $fichiersPartage): static
    {
        if ($this->fichiersPartages->removeElement($fichiersPartage)) {
            $fichiersPartage->removeUser($this);
        }

        return $this;
    }

    public function getJSON()
    {
        return json_encode([
            "email" => $this->getEmail(),
            "id" => $this->getId(),
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname(),
            "isverified" => $this->isVerified,
        ]);
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }
}
