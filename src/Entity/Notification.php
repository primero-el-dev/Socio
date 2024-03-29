<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Entity\Interface\UserOwned;
use App\Entity\Trait\Create;
use App\Entity\Trait\SoftDelete;
use App\Entity\Trait\Update;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Notification implements Entity
{
    use Create;
    use SoftDelete;

    public const REQUEST_FRIEND_RELATION = 'REQUEST_FRIEND_RELATION';
    public const BREAK_FRIEND_RELATION = 'BREAK_FRIEND_RELATION';
    public const ACCEPT_FRIEND_RELATION = 'ACCEPT_FRIEND_RELATION';
    public const REQUEST_MOTHER_RELATION = 'REQUEST_MOTHER_RELATION';
    public const BREAK_MOTHER_RELATION = 'BREAK_MOTHER_RELATION';
    public const ACCEPT_MOTHER_RELATION = 'ACCEPT_MOTHER_RELATION';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $type;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $subjectIri;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private bool $seen = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifications")
     */
    private ?User $recipient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $messageSubject;

    public function __construct()
    {
        //
    }

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getSubjectIri(): ?string
    {
        return $this->subjectIri;
    }

    public function setSubjectIri(string $subjectIri): self
    {
        $this->subjectIri = $subjectIri;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(User $recipient): self
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients[] = $recipient;
        }

        return $this;
    }

    public function removeRecipient(User $recipient): self
    {
        $this->recipients->removeElement($recipient);

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getMessageSubject(): ?string
    {
        return $this->messageSubject;
    }

    public function setMessageSubject(string $messageSubject): self
    {
        $this->messageSubject = $messageSubject;

        return $this;
    }
}
