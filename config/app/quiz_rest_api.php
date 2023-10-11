<?php

declare(strict_types=1);

use App\Quiz\RestApi\ApiController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ApiController::class)->tag('controller.service_arguments');
};
