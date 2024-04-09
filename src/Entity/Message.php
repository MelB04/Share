<?php

namespace App\Entity;

use App\Repository\MessageRepository;
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
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;

#[ApiResource(paginationItemsPerPage: 10,operations: [
    new GetCollection(normalizationContext: ['groups' => 'message:list']),
    new Post(),
    new Get(normalizationContext: ['groups' => 'message:item']),
    new Put(),
    new Patch(security: "object.user == user"),
    new Delete(security: "is_granted('ROLE_ADMIN') or object.user == user"),
    ],)]

#[ApiFilter(ExistsFilter::class, properties: ['parent'])]
#[ApiFilter(SearchFilter::class, properties: ['user' => 'exact', 'parent'=> 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['id' => 'ASC', 'title' => 'ASC'])]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['message:list', 'message:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['message:list', 'message:item'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['message:list', 'message:item'])]
    private ?\DateTimeInterface $datePost = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['message:list', 'message:item'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:list', 'message:item'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'messages')]
    #[Groups(['message:list', 'message:item'])]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDatePost(): ?\DateTimeInterface
    {
        return $this->datePost;
    }

    public function setDatePost(\DateTimeInterface $datePost): static
    {
        $this->datePost = $datePost;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(self $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setParent($this);
        }

        return $this;
    }

    public function removeMessage(self $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getParent() === $this) {
                $message->setParent(null);
            }
        }

        return $this;
    }
}
