<?php

declare(strict_types=1);

use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\QuestionPool\RandomPool;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(RandomPool::class);
    $services->alias(QuestionPoolInterface::class, RandomPool::class);
};
