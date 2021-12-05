<?php

namespace App\DataValidator;

use App\DataValidator\DataValidator;
use App\Entity\User;
use App\Validator\UniqueIgnoreDeleted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Regex;

class PhoneDataValidator extends DataValidator
{
	public function __construct(
		TranslatorInterface $translator,
		private EntityManagerInterface $entityManager
	) {
		parent::__construct($translator);
	}

	protected function getValidators(): array
	{
		return [
			'phone' => [
				new Regex(
					pattern: '/^\+?\d{7,20}$/',
					message: $this->translator->trans(
					'entity.user.phone.regex.message')
				),
		        new UniqueIgnoreDeleted(
		        	className: User::class,
		        	valueColumn: 'phone',
		        	message: $this->translator->trans(
		        		'entity.user.phone.uniqueIgnoreDeleted.message'),
		        	entityManager: $this->entityManager
		        ),
			],
		];
	}
}