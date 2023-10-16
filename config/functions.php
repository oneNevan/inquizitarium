<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Symfony\Config\DoctrineConfig;

/**
 * @param class-string $entity
 *
 * @throws \ReflectionException
 */
function configureDoctrineMapping(
    DoctrineConfig $config,
    string $entity,
    string $manager = 'default',
    string $mappingType = 'attribute',
): void {
    $reflector = new \ReflectionClass($entity);

    $config->orm()
        ->entityManager($manager)
        ->mapping(str_replace('\\', '', $reflector->getNamespaceName()))
        ->type($mappingType)
        ->dir(dirname($reflector->getFileName()))
        ->prefix($reflector->getNamespaceName())
        ->isBundle(false);
}
