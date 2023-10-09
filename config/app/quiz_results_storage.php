<?php

declare(strict_types=1);

use App\Infrastructure\Config\DoctrineMappingConfigurator;
use App\Quiz\ResultsStorage\Orm;
use App\Quiz\ResultsStorage\QuizCheckedEventHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (ContainerConfigurator $configurator, DoctrineConfig $doctrineConfig): void {
    DoctrineMappingConfigurator::configure($doctrineConfig, entity: Orm\Result::class);

    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(Orm\ResultRepository::class);
    $services->set(Orm\QuestionRepository::class);
    $services->set(Orm\AnswerRepository::class);

    $services->set(QuizCheckedEventHandler::class)->tag('messenger.message_handler', [
        'bus' => 'event.bus',
    ]);
};
