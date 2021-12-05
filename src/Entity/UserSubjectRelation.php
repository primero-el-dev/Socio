<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use App\Entity\User;
use App\Util\EntityUtils;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserSubjectRepository::class)
 * @ORM\Table(name="user_subject_relation")
 */
class UserSubjectRelation implements Entity
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_MEMBER = 'ROLE_MEMBER';
    public const REQUEST_MEMBERSHIP = 'REQUEST_MEMBERSHIP';
    public const READ_COMMENT = 'READ_COMMENT';
    public const CREATE_COMMENT = 'CREATE_COMMENT';
    public const REACT_COMMENT = 'REACT_COMMENT';
    public const REPORT_COMMENT = 'REPORT_COMMENT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[ApiProperty(identifier: true)]
    protected ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected ?string $action;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    protected ?User $who = null;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected ?bool $denied;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeInterface $terminatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $subjectIri;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getWho(): ?User
    {
        return $this->who;
    }

    public function setWho(?User $who): self
    {
        $this->who = $who;

        return $this;
    }

    public function getWhom(): ?User
    {
        return $this->whom;
    }

    public function setWhom(User $whom): self
    {
        $this->whom = $whom;

        return $this;
    }

    public function getDenied(): ?bool
    {
        return $this->denied;
    }

    public function setDenied(bool $denied = true): self
    {
        $this->denied = $denied;

        return $this;
    }

    public function getTerminatedAt(): ?\DateTimeInterface
    {
        return $this->terminatedAt;
    }

    public function setTerminatedAt(?\DateTimeInterface $terminatedAt): self
    {
        $this->terminatedAt = $terminatedAt;

        return $this;
    }

    public function isDenied(): bool
    {
        return $this->denied && 
            (!$this->terminatedAt ||
            $this->terminatedAt >= (new \DateTime()));
    }

    public function isDeniedForUser(User $user)
    {
        return $this->isDenied() && (!$this->who || EntityUtils::areSame($this->who, $user));
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
}
