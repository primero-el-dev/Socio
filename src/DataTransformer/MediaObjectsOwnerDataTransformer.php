<?php

namespace App\DataTransformer;

use App\Entity\Interface\HasMediaObjects;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class MediaObjectsOwnerDataTransformer implements DataTransformerInterface
{
	#[Groups(['read:user'])]
    #[SerializedName('description')]
	public function transform($input, string $to, array $context = [])
    {
    	dd('ok');
        // $cheeseListing = new ;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        dd('o');
        // if ($data instanceof $to) {
        //     return false;
        // }

        return is_array($data) && (new $to) instanceof HasMediaObjects;
    }
}