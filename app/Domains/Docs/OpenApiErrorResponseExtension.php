<?php

namespace App\Domains\Docs;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Reference;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\ArrayType;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\RouteInfo;

final class OpenApiErrorResponseExtension extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        if (! str_starts_with($routeInfo->route->uri(), 'v3/')) {
            return;
        }

        foreach ($operation->responses ?? [] as $response) {
            if ($response instanceof Reference) {
                $response = $response->resolve();
            }

            if (! $response instanceof Response) {
                continue;
            }

            $schema = match ((int) $response->code) {
                404, 410 => $this->messageSchema(),
                422 => $this->validationSchema(),
                default => null,
            };

            if ($schema !== null) {
                $response->setContent('application/json', Schema::fromType($schema));
            }
        }
    }

    private function messageSchema(): ObjectType
    {
        return (new ObjectType)
            ->addProperty('message', new StringType)
            ->setRequired(['message']);
    }

    private function validationSchema(): ObjectType
    {
        $validationMessages = (new ArrayType)->setItems(new StringType);
        $errors = (new ObjectType)->additionalProperties($validationMessages);

        return (new ObjectType)
            ->addProperty('message', new StringType)
            ->addProperty('errors', $errors)
            ->setRequired(['message', 'errors']);
    }
}
