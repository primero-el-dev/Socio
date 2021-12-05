<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\UserOwned;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UriGeneratorInterface;

class UserOwnedEntityDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private Security $security,
        // private UriGeneratorInterface $uriGenerator
    ) {
    }

	public function transform($object, string $to, array $context = [])
    {
        dd('ok');
        $user = $this->security->getUser();
        // $object['author'] = $this->uriGenerator->generateIri();$user->getId();
        dd('');
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        dd('o');
        if ($data instanceof $to) {
            return false;
        }

        return is_array($data) && (new $to) instanceof UserOwned;
    }
}