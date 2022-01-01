<?php

namespace App\Controller\Group;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Configuration\ConfigurationManager;
use App\Entity\Group;
use App\Entity\User;
use App\Entity\UserSubjectRelation;
use App\Http\Request\JsonExtractor;
use App\Repository\Interface\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CreateGroupController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JsonExtractor $jsonExtractor,
        private UserRepositoryInterface $userRepository,
        private IriConverterInterface $iriConverter
    ) {
    }

    public function __invoke(Request $request)
    {
        $user = $this->userRepository->find($this->getUser()->getId());
        $data = $this->jsonExtractor->extract($request);

        $group = $this->createGroup($data);
        $this->createUserRelations($user, $group);

        return $group;
    }

    private function createGroup(array $data): Group
    {
        $group = new Group();
        $group->setName($data['name'] ?? null);
        $group->setSlug($data['slug'] ?? null);
        $group->setDescription($data['description'] ?? '');
        $group->setConfiguration(ConfigurationManager::getDefaultForGroup());

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $group;
    }

    private function createUserRelations(User $user, Group $group): void
    {
        $groupIri = $this->iriConverter->getIriFromItem($group);

        $this->createUserRelation($user, UserSubjectRelation::ROLE_MEMBER, $groupIri);
        $this->createUserRelation($user, UserSubjectRelation::ROLE_ADMIN, $groupIri);
        $this->createUserRelation($user, UserSubjectRelation::READ_GROUP, $groupIri);
        $this->createUserRelation($user, UserSubjectRelation::UPDATE_GROUP, $groupIri);
        $this->createUserRelation($user, UserSubjectRelation::DELETE_GROUP, $groupIri);
    }

    private function createUserRelation(User $user, string $action, string $groupIri): void
    {
        $relation = new UserSubjectRelation();
        $relation->setWho($user);
        $relation->setAction($action);
        $relation->setSubjectIri($groupIri);

        $this->entityManager->persist($relation);
        $this->entityManager->flush();
    }
}
