<?php

namespace App\DataValidator;

use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class DataValidator
{
	protected array $errors = [];

	public function __construct(
		protected TranslatorInterface $translator
	) {
	}

	public function validate(array $data): bool
	{
		$this->errors = [];

		foreach ($this->getValidators() as $key => $validators) {
			$this->validateSingle($data[$key] ?? null, $key, $validators);
		}

		$this->afterValidation($data);
		
		return empty($this->errors);
	}

	protected function validateSingle($value, string $key, array $validators): void
	{
		$validator = Validation::createValidator();
        $violations = $validator->validate($value, $validators);

        if (count($violations)) {
        	$this->errors[$key] = [];
        	foreach ($violations as $violation) {
        		$this->errors[$key] = $violation->getMessage();
        	}
        }
	}

	abstract protected function getValidators(): array;

	public function getErrors(): array
	{
		return $this->errors;
	}

	protected function afterValidation(array $data): void
	{
		//
	}
}