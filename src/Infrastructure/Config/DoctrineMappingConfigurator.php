<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use Symfony\Config\DoctrineConfig;

final readonly class DoctrineMappingConfigurator
{
    /**
     * @psalm-suppress UnusedConstructor
     */
    private function __construct()
    {
    }

    /**
     * @param class-string $entity
     *
     * @throws \ReflectionException
     */
    public static function configure(
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
}
