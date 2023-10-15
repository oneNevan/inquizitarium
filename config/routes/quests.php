<?php

declare(strict_types=1);

use App\Quests\Infrastructure\Web\TheShelterDemoController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import(TheShelterDemoController::class, 'attribute');
};
