<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use App\Entity\Interface\HasConfiguration;
use App\Entity\Timeline;
use App\Entity\Trait\Configuration;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Repository\UserRepository;
use App\Security\Roles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteable;
use Gedmo\Timestampable\Traits\Timestampable;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/** 
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(
 *      name="app_user",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="login_unique", 
 *              columns={"login"},
 *              options={"where": "(deleted_at IS NULL)"}
 *          ),
 *          @ORM\UniqueConstraint(
 *              name="email_unique", 
 *              columns={"email"},
 *              options={"where": "(deleted_at IS NULL)"}
 *          ),
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
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'surname' => 'partial',
])]
class User implements Entity, HasConfiguration, UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
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
    #[ApiProperty(identifier: true)]
    protected ?int $id;


    /**
     * @ORM\Column(type="string", length=180)
     */
    #[Groups(['read:user:self', 'read:user:email', 'insert:user'])]
    #[Assert\NotBlank(message: 'entity.user.email.notBlank.message')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'entity.user.email.length.minMessage',
        maxMessage: 'entity.user.email.length.maxMessage'
    )]
    #[Assert\Email(message: 'entity.user.email.email.message')]
    protected ?string $email;


    /**
     * @ORM\Column(type="json")
     */
    #[Groups(['read:user:self'])]
    protected array $roles = [];


    /**
     * @ORM\Column(type="string")
     */
    #[Groups(['write:user'])]
    protected ?string $password;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:user:self', 'read:user:name', 'insert:user'])]
    #[Assert\Regex(
        pattern: '/^\w+$/',
        message: 'entity.user.name.regex.message'
    )]
    #[Assert\Length(max: 60, maxMessage: 'entity.user.name.length.maxMessage')]
    protected ?string $name;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:user:self', 'read:user:surname', 'insert:user'])]
    #[Assert\Regex(
        pattern: '/^\w+$/',
        message: 'entity.user.surname.regex.message'
    )]
    #[Assert\Length(max: 60, maxMessage: 'entity.user.surname.length.maxMessage')]
    protected ?string $surname;


    /**
     * @ORM\Column(type="date_immutable")
     */
    #[Groups(['read:user', 'read:user:birth', 'insert:user'])]
    #[Assert\NotNull(message: 'entity.user.birth.notNull.message')]
    #[Assert\Date(message: 'entity.user.birth.date.message')]
    protected ?\DateTimeInterface $birth;


    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     */
    protected Collection $posts;


    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    protected Collection $comments;


    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    #[Groups('read:user:self', 'read:user:phone', 'write:user')]
    #[Assert\Regex(
        pattern: '/^\+?\d{7,20}$/',
        message: 'entity.user.phone.regex.message'
    )]
    protected ?string $phone;


    /**
     * @ORM\OneToMany(targetEntity=Reaction::class, mappedBy="author")
     */
    protected Collection $reactions;


    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    #[Groups(['read:user:self'])]
    protected bool $verified = false;


    /**
     * @ORM\OneToOne(targetEntity=Timeline::class, mappedBy="user")
     */
    protected ?Timeline $timeline;


    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="recipient")
     */
    protected Collection $notifications;


    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    #[Groups(['read:user:self'])]
    protected bool $phoneVerified = false;


    /**
     * @ORM\Column(type="json", options={"default":"[]"})
     */
    #[Groups(['read:user:self'])]
    protected array $configuration = [];


    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    #[Groups(['book:read'])]
    public ?string $contentUrl = null;


    /**
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="filePath")
     */
    #[Groups(['book:write'])]
    public ?File $file = null;


    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $filePath = null;


    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['insert:user', 'read:user'])]
    #[Assert\NotBlank(
        message: 'entity.user.slug.notBlank.message'
    )]
    #[Assert\Regex(
        pattern: '/^[\w\-]+$/',
        message: 'entity.user.slug.regex.message'
    )]
    #[Assert\Length(
        min: 5,
        max: 60,
        minMessage: 'entity.user.slug.length.minMessage',
        maxMessage: 'entity.user.slug.length.maxMessage'
    )]
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['insert:user', 'read:user'])]
    #[Assert\NotBlank(message: 'entity.user.login.notBlank.message')]
    #[Assert\Regex(
        pattern: '/^[\w\-]+$/',
        message: 'entity.user.login.regex.message'
    )]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'entity.user.login.length.minMessage',
        maxMessage: 'entity.user.login.length.maxMessage'
    )]
    private ?string $login;


    public function __construct()
    {
        $this->roles = Roles::getDefaultForUser();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->reactions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
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
        $this->roles[] = 'ROLE_USER';
        $this->roles = array_unique(array_values($this->roles));

        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_unique(array_values($roles));

        return $this;
    }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        $this->roles = array_filter($this->roles, fn($r) => $r !== $role);

        return $this;
    }

    public function hasRole($role): bool
    {
        return in_array($role, $this->roles);
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

    public function getBirth(): ?\DateTimeInterface
    {
        return $this->birth;
    }

    public function setBirth(\DateTimeInterface $birth): self
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
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
            $reaction->setAuthor($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            if ($reaction->getAuthor() === $this) {
                $reaction->setAuthor(null);
            }
        }

        return $this;
    }

    public function getVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getTimeline(): ?Timeline
    {
        return $this->timeline;
    }

    public function setTimeline(Timeline $timeline): self
    {
        $this->timeline = $timeline;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->addRecipient($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeRecipient($this);
        }

        return $this;
    }

    public function getPhoneVerified(): ?bool
    {
        return $this->phoneVerified;
    }

    public function setPhoneVerified(bool $phoneVerified): self
    {
        $this->phoneVerified = $phoneVerified;

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

    public function getTextIdentificator(): string
    {
        return ($this->name && $this->surname)
            ? $this->name . ' ' . $this->surname
            : $this->login;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }
}
