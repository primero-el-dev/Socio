<?php

namespace App\DataValidator;

use App\DataValidator\DataValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RepeatedPasswordDataValidator extends DataValidator
{
	protected function getValidators(): array
	{
		return [
			'password' => [
				new NotBlank(message: $this->translator->trans(
					'entity.user.password.notBlank.message')),
                new Length(
                    min: 15, 
                    max: 60,
                    minMessage: $this->translator->trans(
                    	'entity.user.password.length.minMessage'),
                    maxMessage: $this->translator->trans(
                    	'entity.user.password.length.maxMessage')
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