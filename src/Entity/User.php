<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Gedmo\Timestampable\Traits\Timestampable;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="app_user")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'surname' => 'partial',
])]
class User extends Entity 
implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    use Create;
    use Update;
    use SoftDelete;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[ApiProperty(identifier: true)]
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    #[Groups(['read:user', 'insert:user'])]
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    #[Groups(['read:user:self'])]
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    #[Groups(['write:user'])]
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:user', 'insert:user'])]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:user', 'insert:user'])]
    private ?string $surname;

    /**
     * @ORM\Column(type="date_immutable")
     */
    #[Groups(['read:user', 'insert:user'])]
    private ?\DateTimeInterface $birth;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     */
    private ArrayCollection $posts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    private ArrayCollection $comments;

    /**
     * @ORM\Column(type="json", options={"default": "[]"})
     */
    private array $permissions = [];

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="whom")
     */
    private ArrayCollection $relationsWhereSubject;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public static function createFromPayload($id, array $payload)
    {
        return (new self())
            ->setId((int) $id)
            ->setEmail($payload['username'])
            ->setRoles($payload['roles'])
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_unique($roles);

        return $this;
    }

    public function addRole(string $role): self
    {
        if (!is_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        $this->roles = array_filter($this->roles, fn($r) => $r !== $role);

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getBirth(): ?\DateTimeImmutable
    {
        return $this->birth;
    }

    public function setBirth(\DateTimeImmutable $birth): self
    {
        $this->birth = $birth;

        return $this;
    }

    public function getAge(): int
    {
        return (int) date_diff($this->birth, new \DateTime(), true)->format('y');
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        if (!isset($this->permissions[$permission])) {
            return false;
        }

        return (bool) is_string($this->permissions[$permission])
            ? ($this->permissions[$permission] <= (new \DateTime())->format('Y-m-d H:i:s'))
            : $this->permissions[$permission];
    }

    public function removePermission(string $permission, ?\DateTime $deadline = null): self
    {
        if (isset($this->permissions[$permission])) {
            $this->permissions[$permission] = 
                ($deadline) ? $deadline->format('Y-m-d H:i:s') : false;
        }

        return $this;
    }

    public function addPermission(string $permission): self
    {
        $this->permissions[$permission] = true;

        return $this;
    }
}
