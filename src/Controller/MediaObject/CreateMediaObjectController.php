<?php

namespace App\Controller\MediaObject;

use App\Entity\MediaObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreateMediaObjectController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(Request $request)
    {
        $filename = 'file';
        $uploadedFile = $request->files->get($filename);
        
        if (!$uploadedFile) {
            throw new BadRequestHttpException(
                $this->translator->trans(sprintf(
                    'notification.error.fileMissing',
                    $filename
                ))
            );
        }

        $mediaObject = new MediaObject();
        $mediaObject->file = $uploadedFile;
        $mediaObject->setOwnerIri('/api/users/2');
        $mediaObject->setType('type');

        $this->entityManager->persist($mediaObject);
        $this->entityManager->flush();

        return $mediaObject;
    }
}
