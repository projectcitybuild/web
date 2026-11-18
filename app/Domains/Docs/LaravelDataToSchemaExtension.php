<?php

namespace App\Domains\Docs;

use Dedoc\Scramble\Extensions\TypeToSchemaExtension;
use Dedoc\Scramble\Infer\Definition\PropertyVisibility;
use Dedoc\Scramble\Infer\Scope\GlobalScope;
use Dedoc\Scramble\Infer\Services\ReferenceTypeResolver;
use Dedoc\Scramble\Support\Generator\ClassBasedReference;
use Dedoc\Scramble\Support\Generator\Reference;
use Dedoc\Scramble\Support\Generator\Types\Type as OpenApiType;
use Dedoc\Scramble\Support\Type\ArrayItemType_;
use Dedoc\Scramble\Support\Type\KeyedArrayType;
use Dedoc\Scramble\Support\Type\ObjectType;
use Dedoc\Scramble\Support\Type\Reference\PropertyFetchReferenceType;
use Dedoc\Scramble\Support\Type\Type;
use Dedoc\Scramble\Support\Type\Union;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class LaravelDataToSchemaExtension extends TypeToSchemaExtension
{
    public function shouldHandle(Type $type): bool
    {
        return $type instanceof ObjectType
            && class_exists($type->name)
            && is_a($type->name, Data::class, true);
    }

    public function toSchema(Type $type): ?OpenApiType
    {
        if (! $type instanceof ObjectType) {
            return null;
        }

        $definition = $this->infer->index->getClass($type->name);

        if (! $definition) {
            return null;
        }

        $items = [];

        foreach ($definition->getData()->properties as $name => $property) {
            if ($property->visibility !== PropertyVisibility::Public) {
                continue;
            }

            $propertyType = ReferenceTypeResolver::getInstance()->resolve(
                new GlobalScope,
                new PropertyFetchReferenceType($type, $name),
            );
            $isOptional = false;

            if ($propertyType instanceof Union) {
                $propertyTypes = array_values(array_filter(
                    $propertyType->types,
                    function (Type $propertyType) use (&$isOptional): bool {
                        if ($propertyType instanceof ObjectType && is_a($propertyType->name, Optional::class, true)) {
                            $isOptional = true;

                            return false;
                        }

                        return true;
                    },
                ));

                $propertyType = Union::wrap(...$propertyTypes);
            } elseif ($propertyType instanceof ObjectType && is_a($propertyType->name, Optional::class, true)) {
                $isOptional = true;

                continue;
            }

            $items[] = new ArrayItemType_($name, $propertyType, isOptional: $isOptional);
        }

        return $items === []
            ? null
            : $this->openApiTransformer->transform(new KeyedArrayType($items));
    }

    public function reference(ObjectType $type): ?Reference
    {
        return ClassBasedReference::create('schemas', $type->name, $this->components);
    }
}
