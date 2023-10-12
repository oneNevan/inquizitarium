<?php

declare(strict_types=1);

use App\Quiz\Domain\CheckedQuiz\Quiz as CheckedQuiz;
use App\Quiz\Domain\NewQuiz\Quiz as NewQuiz;
use App\Quiz\RestApi\ApiController;
use App\Quiz\RestApi\Doc\ModelDescriber\ExpressionModelDescriber;
use App\Quiz\RestApi\Doc\ModelDescriber\UuidModelDescriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\NelmioApiDocConfig;

return static function (ContainerConfigurator $configurator, NelmioApiDocConfig $apiDocConfig): void {
    $models = $apiDocConfig->models();
    $models->names()->type(NewQuiz::class)->alias('NewQuiz');
    $models->names()->type(CheckedQuiz::class)->alias('CheckedQuiz');

    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ApiController::class)->tag('controller.service_arguments');

    $services->set(UuidModelDescriber::class);
    $services->set(ExpressionModelDescriber::class);
};
