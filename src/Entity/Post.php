<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Entity\Thread;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Post extends Entity
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
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity=Thread::class, inversedBy="posts")
     */
    private ?Thread $thread;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post")
     */
    private ArrayCollection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
