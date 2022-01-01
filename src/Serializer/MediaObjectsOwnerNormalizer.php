<?php

namespace App\Serializer;

use App\Entity\Interface\HasMediaObjects;
use App\Entity\MediaObject;
use App\Repository\Interface\MediaObjectRepositoryInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

final class MediaObjectsOwnerNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'MEDIA_OBJECTS_OWNER_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private StorageInterface $storage,
        private MediaObjectRepositoryInterface $mediaObjectRepository
    ) {
    }

    public function normalize(
        $object, 
        ?string $format = null, 
        array $context = []
    ): array|string|int|float|bool|\ArrayObject|null 
    {
        $context[self::ALREADY_CALLED] = true;

        $object->setMediaObjects($this->mediaObjectRepository->findByOwner($object));

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(
        $data, 
        ?string $format = null, 
        array $context = []
    ): bool 
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof HasMediaObjects;
    }
}