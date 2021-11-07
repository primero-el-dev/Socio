<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $this->hidePathsToHide($openApi);

        $this->addBearerAuth($openApi);
        $this->addCreadentialsSchema($openApi);

        // $pathItem = $openApi->getPaths()->getPath('/api/users/{id}');
        // $operation = $pathItem->getGet();

        // $openApi->getPaths()->addPath('/api/users/{id}', $pathItem->withGet(
        //     $operation->withParameters(array_merge(
        //         $operation->getParameters(),
        //         [new Model\Parameter('fields', 'query', 'Fields to remove of the output')]
        //     ))
        // ));

        // $openApi = $openApi->withInfo((new Model\Info('New Title', 'v2', 'Description of my custom API'))->withExtensionProperty('info-key', 'Info value'));
        // $openApi = $openApi->withExtensionProperty('key', 'Custom x-key value');
        // $openApi = $openApi->withExtensionProperty('x-value', 'Custom x-value value');

        return $openApi;
    }

    private function hidePathsToHide(OpenApi &$openApi): void
    {
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }
    }

    private function addBearerAuth(OpenApi &$openApi): void
    {
        $schemes = $openApi->getComponents()->getSecuritySchemes();
        $schemes['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ]);
    }

    private function addCreadentialsSchema(OpenApi &$openApi): void
    {
        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@mail.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'admin123',
                ],
            ],
        ]);
    }
}