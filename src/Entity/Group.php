<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Entity\Interface\HasComments;
use App\Entity\Interface\HasConfiguration;
use App\Entity\Trait\Configuration;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(
 *      name="`group`",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="slug_unique", 
 *              columns={"slug"},
 *              options={"where": "(deleted_at IS NULL)"}
 *          )
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Group implements Entity, HasComments, HasConfiguration
{
    use Create;
    use Update;
    use SoftDelete;
    use Configuration;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['write:group', 'read:group'])]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: 'entity.group.name.length.minMessage'
        maxMessage: 'entity.group.name.length.maxMessage'
    )]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['insert:group', 'read:group'])]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'entity.group.slug.length.minMessage',
        maxMessage: 'entity.group.slug.length.maxMessage'
    )]
    private ?string $slug;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['write:group', 'read:group'])]
    #[Assert\Length(
        max: 2048,
        maxMessage: 'entity.group.description.length.maxMessage'
    )]
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="group")
     */
    private Collection $comments;

    /**
     * @ORM\Column(type="json", options={"default":"[]"})
     */
    private array $configuration = [];

    public function __construct()
    {
        $this->threads = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

       return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Collection $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setGroup($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGroup() === $this) {
                $comment->setGroup(null);
            }
        }

        return $this;
    }
}
