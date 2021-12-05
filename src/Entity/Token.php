<?php

namespace App\Entity;

use App\Entity\Entity;
use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token implements Entity
{
    public const EMAIL_VERIFICATION_TYPE = 'EMAIL_VERIFICATION_TOKEN';
    public const PHONE_VERIFICATION_TYPE = 'PHONE_VERIFICATION_TOKEN';
    public const RESET_PASSWORD_TYPE = 'RESET_PASSWORD_EMAIL_TOKEN';

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
     * @ORM\Column(type="string", length=255)
     */
    private ?string $value;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private ?\DateTimeInterface $expiresAt;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function hasExpired(): bool
    {
        return $this->expiresAt->format('Y-m-d H:i:s') < 
            (new \DateTime())->format('Y-m-d H:i:s');
    }
}
