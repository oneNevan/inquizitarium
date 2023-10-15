<?php

declare(strict_types=1);

use App\Math\Infrastructure\ApiDoc\ModelDescriber\ExpressionModelDescriber;
use App\Math\Infrastructure\Symfony\Serializer\ExpressionNormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ExpressionNormalizer::class);
    $services->set(ExpressionModelDescriber::class);
};
