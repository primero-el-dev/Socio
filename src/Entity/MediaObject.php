<?php

namespace App\Entity;

use App\Repository\MediaObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=MediaObjectRepository::class)
 * @Vich\Uploadable
 */
class MediaObject
{
    public const MEDIA_PATH = 'media';

    public const PROFILE_PICTURE_TYPE = 'PROFILE_PICTURE_TYPE';
    public const BACKGROUND_PICTURE_TYPE = 'BACKGROUND_PICTURE_TYPE';
    public const GALLERY_PICTURE_TYPE = 'GALLERY_PICTURE_TYPE';
    public const COMMENT_PICTURE_TYPE = 'COMMENT_PICTURE_TYPE';
    public const GALLERY_VIDEO_TYPE = 'GALLERY_VIDEO_TYPE';
    public const COMMENT_VIDEO_TYPE = 'COMMENT_VIDEO_TYPE';
    public const GALLERY_GIF_TYPE = 'GALLERY_GIF_TYPE';
    public const COMMENT_GIF_TYPE = 'COMMENT_GIF_TYPE';
    public const APP_GIF_TYPE = 'APP_GIF_TYPE';
    public const APP_STICKER_TYPE = 'APP_STICKER_TYPE';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    #[ApiProperty(iri: 'http://schema.org/contentUrl')]
    #[Groups(['read:media_object'])]
    private ?string $contentUrl = null;

    /**
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="filePath")
     */
    #[Assert\NotNull(
        message: 'entity.mediaObject.file.notNull.message',
        groups: ['create:media_object']
    )]
    #[Assert\File(
        maxSize: '1024k',
        maxSizeMessage: ''
    )]
    public ?File $file = null;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $filePath = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotNull(message: 'entity.mediaObject.ownerIri.notNull.message')]
    private ?string $ownerIri;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function setContentUrl(string $contentUrl): self
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    public function getOwnerIri(): ?string
    {
        return $this->ownerIri;
    }

    public function setOwnerIri(string $ownerIri): self
    {
        $this->ownerIri = $ownerIri;

        return $this;
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
}
