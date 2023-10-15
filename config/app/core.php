<?php

declare(strict_types=1);

use App\Core\Application\CommandBusInterface;
use App\Core\Application\EventBusInterface;
use App\Core\Infrastructure\ApiDoc\ModelDescriber\UuidModelDescriber;
use App\Core\Infrastructure\Symfony\Messenger\CommandBus;
use App\Core\Infrastructure\Symfony\Messenger\EventBus;
use App\Core\Infrastructure\Symfony\Serializer\UuidNormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator, ContainerBuilder $builder): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CommandBus::class);
    $services->alias(CommandBusInterface::class, CommandBus::class);
    $services->set(EventBus::class);
    $services->alias(EventBusInterface::class, EventBus::class);

    $services->set(UuidModelDescriber::class);
    $services->set(UuidNormalizer::class);

    if ('test' === $configurator->env()) {
        $builder->getAlias(CommandBusInterface::class)->setPublic(true);
        $builder->getAlias(EventBusInterface::class)->setPublic(true);
    }
};
