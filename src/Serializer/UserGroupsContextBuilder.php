<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use App\Repository\Interface\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class UserGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
    	private SerializerContextBuilderInterface $decorated,
    	private UserRepositoryInterface $userRepository
    ) {
    }

	public function createFromRequest(
		Request $request, 
		bool $normalization, 
		?array $extractedAttributes = null
	): array
    {
        [$class, $identifier] = $extractedAttributes['identifiers']['id'];
        
    	$context = $this->decorated->createFromRequest(
            $request, 
            $normalization, 
            $extractedAttributes
        );

        if ($class !== User::class) {
            return $context;
        }

    	$id = $request->attributes->get($identifier);
    	$user = $this->userRepository->find($id);

        if (!$user) {
            return $context;
        }
    	
        $user->setConfigurationKeys(['security', 'show_email'], true);

        if ($context['groups'] && $normalization) {
            $setter = $this->getContextSetterForUser($context, $user);

            $setter(['visibility', 'show_email'], 'read:user:email');
            $setter(['visibility', 'show_name'], 'read:user:name');
            $setter(['visibility', 'show_surname'], 'read:user:surname');
            $setter(['visibility', 'show_phone'], 'read:user:phone');
        }

        return $context;
    }

    private function getContextSetterForUser(array &$context, User $user): callable
    {
        return function(array $keys, string $group) use (&$context, $user) {
            if ($user->getConfigurationValue($keys)) {
                $context['groups'][] = $group;
            }
        };
    }
}