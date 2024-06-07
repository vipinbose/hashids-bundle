<?php

declare(strict_types=1);

namespace Vipinbose\HashidsBundle\ValueResolver;

use Doctrine\ORM\Mapping\{
    Id,
    Entity
};
use ReflectionClass;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Throwable;
use Vipinbose\HashidsBundle\Interfaces\HashidsServiceInterface;

class HashIdValueResolver implements ValueResolverInterface
{

    public function __construct(
        private HashidsServiceInterface $hasher,
        private bool $enable = true
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        if (!$this->enable) {
            return [];
        }

        if (is_object($request->attributes->get($argument->getName()))) {
            return [];
        }

        $mappingAttributes = [];

        try {
            $className = $argument->getType();
            if (!$className) {
                return [];
            }
            $reflection = (new ReflectionClass($className));
            $attributes = $reflection->getAttributes(Entity::class);

            if (count($attributes) !== 0) {
                foreach ($reflection->getProperties() as $reflectionProperty) {
                    $reflectionPropertyAttributes = $reflectionProperty->getAttributes(Id::class);
                    if (count($reflectionPropertyAttributes) !== 0) {
                        $mappingAttributes[] = $reflectionProperty->getName();
                    }
                }
            }
        } catch (Throwable) {
        }

        $mapEntity = $argument->getAttributes(MapEntity::class, ArgumentMetadata::IS_INSTANCEOF);
        /** @var MapEntity|null $mapEntity */
        $mapEntity = $mapEntity[0] ?? null;

        if ($mapEntity !== null && $mapEntity->mapping !== null) {
            $mappingAttributes = array_merge($mappingAttributes, array_keys($mapEntity->mapping));
        }

        if (!class_exists($argument->getName())) {
            $mappingAttributes[] = $argument->getName();
        }

        /** @var string|null $hash */
        $hash = $request->attributes->get("hashid");

        $hash = (string) $hash;
        $hashids = $this->hasher->decode($hash);
        if ($this->hasHashidDecoded($hashids)) {
            $request->attributes->set("id", current($hashids));
        }

        return [];
    }


    private function hasHashidDecoded(mixed $hashids): bool
    {
        return $hashids && is_iterable($hashids);
    }
}
