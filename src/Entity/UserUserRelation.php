<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="user_user_relation")
 */
class UserUserRelation extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[ApiProperty(identifier: true)]
    protected ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    protected ?User $who = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="relationsWhereSubject")
     */
    protected User $whom;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
