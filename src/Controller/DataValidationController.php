<?php

namespace App\Controller;

use App\Http\Request\DataExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class DataValidationController extends AbstractController
{
    public function __construct(
        protected array $dataValidators,
        protected DataExtractor $dataExtractor
    ) {
    }

    public function __invoke(Request $request)
    {
        if ($this->dataExtractor->hasData($request)) {
            $data = $this->dataExtractor->extract($request);
            $errors = $this->validate($data);

            if (!empty($errors)) {
                return new JsonResponse(['errors' => $errors]);
            }
        }

        return $this->afterValidation($data);
    }

    protected function validate(array $data): array
    {
        foreach ($this->dataValidators as $dataValidator) {
            $dataValidator->validate($data);
        }

        return array_merge(
            array_map(fn($dv) => $dv->getErrors(), $this->dataValidators)
        );
    }

    abstract protected function afterValidation(array $data);
}
