<?php

namespace App\Entity;

use App\Entity\Comment;
use App\Entity\Entity;
use App\Entity\Interface\UserOwned;
use App\Entity\User;
use App\Repository\ReactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReactionRepository::class)
 */
class Reaction implements Entity, UserOwned
{
    public const TYPES = [
        'LIKE',
        'DISLIKE',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:comment'])]
    private ?string $type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reactions")
     */
    #[Groups(['read:comment'])]
    private ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Comment $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
