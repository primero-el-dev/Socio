<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Entity\Interface\HasComments;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Entity\User;
use App\Entity\Interface\UserOwned;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
#[
    ApiFilter(ExistsFilter::class, properties: ['parent']),
    ApiFilter(BooleanFilter::class, properties: ['accepted']),
    ApiFilter(SearchFilter::class, properties: ['content' => 'partial']),
    ApiFilter(DateFilter::class, strategy: DateFilter::EXCLUDE_NULL)
]
class Comment implements Entity, HasComments, UserOwned
{
    use Create;
    use Update;
    use SoftDelete;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text")
     */
    #[
        Groups(['write:comment', 'read:comment']),
        Assert\NotNull(message: 'entity.comment.content.notNull.message'),
        Assert\Length(
            min: 1,
            max: 2000,
            minMessage: 'entity.comment.content.notNull.message',
            maxMessage: 'entity.comment.content.length.maxMessage'
        )
    ]
    private ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     */
    #[Groups(['read:comment'])]
    private ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="comments")
     */
    #[Groups(['insert:comment', 'read:comment'])]
    private ?Group $group;

    /**
     * @ORM\ManyToOne(targetEntity=Thread::class, inversedBy="comments")
     */
    #[Groups(['insert:comment', 'read:comment'])]
    private ?Thread $thread;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="comments")
     */
    #[Groups(['insert:comment'])]
    private ?self $parent;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="parent")
     */
    #[Groups(['read:comment'])]
    private Collection $comments;

    /**
     * @ORM\OneToMany(targetEntity=Reaction::class, mappedBy="comment", orphanRemoval=true)
     */
    #[Groups(['read:comment'])]
    private Collection $reactions;

    /**
     * @ORM\ManyToOne(targetEntity=Timeline::class, inversedBy="comments")
     */
    #[Groups(['insert:comment', 'read:comment'])]
    private ?Timeline $timeline;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    #[Groups(['read:comment'])]
    private bool $accepted = false;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->reactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function hasParent(): bool
    {
        return (bool) $this->parent;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Collection $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getComments() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    public function getBaseComment(): Comment
    {
        return $this->hasParent() ? $this->parent->getBaseComment() : $this;
    }

    /**
     * @return Collection|Reaction[]
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions[] = $reaction;
            $reaction->setComment($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            if ($reaction->getComment() === $this) {
                $reaction->setComment(null);
            }
        }

        return $this;
    }

    public function getTimeline(): ?Timeline
    {
        return $this->timeline;
    }

    public function setTimeline(?Timeline $timeline): self
    {
        $this->timeline = $timeline;

        return $this;
    }

    public function getAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }
}
