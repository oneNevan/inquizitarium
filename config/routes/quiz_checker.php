<?php

declare(strict_types=1);

use App\Quiz\Checker\Infrastructure\ApiController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import(ApiController::class, 'attribute');
};
