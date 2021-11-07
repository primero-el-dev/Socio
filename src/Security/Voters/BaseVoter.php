<?php

namespace App\Security\Voters;

use App\Entity\User;
use App\Util\EntityUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BaseVoter extends Voter
{
    protected static array $actions = [
        'show' => 'SHOW',
        'create' => 'CREATE',
        'update' => 'UPDATE',
        'delete' => 'DELETE',
    ];

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, $this->getRolesForSubject($object));
    }

    protected function getRolesForSubject($object): array
    {
        $className = $this->getShortClassName($object);

        return array_map(
            fn($action) => $this->uncamelize($className) . '_' . $action, 
            static::$actions
        );
    }

    protected function getShortClassName($object): string
    {
        return (new \ReflectionClass($object))->getShortName();
    }

    protected function uncamelize(string $string, string $splitter = '_'): string
    {
        $camel = preg_replace(
            '/(?!^)[[:upper:]][[:lower:]]/', '$0', 
            preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $camel)
        );

        return strtoupper($camel);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $roles = $this->getRolesForSubject($subject);

        foreach ($roles as $key => $role) {
            if ($attribute === $role && $this->{'can'.ucfirst($key)}($user, $subject)) {
                return true;
            }
        }

        return false;
    }

    public function canShow(User $user, $object): bool
    {
        return $user->hasPermission($this->getShortClassName($object).'_SHOW') || 
            method_exists($object, 'getAuthor') ? EntityUtils::areSame($object->getAuthor(), $user) : false;
    }

    public function canCreate(User $user, $object): bool
    {
        return $user->hasPermission($this->getShortClassName($object).'_CREATE');
    }

    public function canUpdate(User $user, $object): bool
    {
        return $user->hasPermission($this->getShortClassName($object).'_UPDATE') || 
            method_exists($object, 'getAuthor') ? EntityUtils::areSame($object->getAuthor(), $user) : false;
    }

    public function canDelete(User $user, $object): bool
    {
        return $user->hasPermission($this->getShortClassName($object).'_DELETE') || 
            method_exists($object, 'getAuthor') ? EntityUtils::areSame($object->getAuthor(), $user) : false;
    }
}
