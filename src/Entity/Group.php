<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Group extends Entity
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
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=Thread::class, mappedBy="group")
     */
    private ArrayCollection $threads;

    public function __construct()
    {
        $this->threads = new ArrayCollection();
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
     * @return Collection|Thread[]
     */
    public function getThreads(): Collection
    {
        return $this->threads;
    }

    public function addThread(Thread $thread): self
    {
        if (!$this->threads->contains($thread)) {
            $this->threads[] = $thread;
            $thread->setGroup($this);
        }

        return $this;
    }

    public function removeThread(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getGroup() === $this) {
                $thread->setGroup(null);
            }
        }

        return $this;
    }
}
