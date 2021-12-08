<?php

namespace App\DataValidator;

use App\DataValidator\DataValidator;
use App\Entity\User;
use App\Validator\UniqueIgnoreDeleted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationDataValidator extends DataValidator
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
			'email' => [
				new NotBlank(message: $this->translator->trans(
					'entity.user.email.notBlank.message')),
		        new Length(
		            min: 5,
		            max: 255,
		            minMessage: $this->translator->trans(
		            	'entity.user.email.length.minMessage'),
		            maxMessage: $this->translator->trans(
		            	'entity.user.email.length.maxMessage')
		        ),
		        new Email(message: $this->translator->trans(
		        	'entity.user.email.email.message')),
		        new UniqueIgnoreDeleted(
		        	className: User::class,
		        	valueColumn: 'email',
		        	message: $this->translator->trans(
		        		'entity.user.email.uniqueIgnoreDeleted.message'),
		        	entityManager: $this->entityManager
		        ),
			],
			'name' => [
				new Regex(
					pattern: '/^\w+$/',
					message: $this->translator->trans(
					'entity.user.name.regex.message')
				),
				new Length(
					max: 60,
					maxMessage: $this->translator->trans(
					'entity.user.name.max.maxMessage')
				),
			],
			'surname' => [
				new Regex(
					pattern: '/^\w+$/',
					message: $this->translator->trans(
					'entity.user.surname.regex.message')
				),
				new Length(
					max: 60,
					maxMessage: $this->translator->trans(
					'entity.user.surname.max.maxMessage')
				),
			],
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
			'birth' => [
				new NotNull(message: $this->translator->trans(
					'entity.user.birth.notNull.message')),
        		new Date(message: $this->translator->trans(
        			'entity.user.birth.date.message')),
    		],
    		'slug' => [
				new Regex(
					pattern: '/^[\w\d]+$/',
					message: $this->translator->trans(
					'entity.user.slug.regex.message')
				),
				new Length(
					min: 5,
					max: 60,
					minMessage: $this->translator->trans(
					'entity.user.slug.max.minMessage'),
					maxMessage: $this->translator->trans(
					'entity.user.slug.max.maxMessage')
				),
		        new UniqueIgnoreDeleted(
		        	className: User::class,
		        	valueColumn: 'slug',
		        	message: $this->translator->trans(
		        		'entity.user.slug.uniqueIgnoreDeleted.message'),
		        	entityManager: $this->entityManager
		        ),
			],
		];
	}

	protected function afterValidation(array $data): void
	{
		if ($this->errors) {
			return;
		}

		if ($data['password'] !== ($data['repeatPassword'] ?? null)) {
			$this->errors['password'] = $this->translator->trans('entity.user.password.notMatch.message');
		}
	}
}