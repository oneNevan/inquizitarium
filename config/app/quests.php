<?php

declare(strict_types=1);

use App\Quests\EnterDwarfsKingdomCommand;
use App\Quests\TheShelterDemoController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(TheShelterDemoController::class)->tag('controller.service_arguments');
    $services->set(EnterDwarfsKingdomCommand::class);
};
