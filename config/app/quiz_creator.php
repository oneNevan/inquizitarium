<?php

declare(strict_types=1);

use App\Quiz\Creator\CreateQuizHandler;
use App\Quiz\Creator\QuizFactory;
use App\Quiz\Domain\NewQuiz\QuizFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CreateQuizHandler::class)->tag('messenger.message_handler', [
        'bus' => 'command.bus',
    ]);

    $services->set(QuizFactory::class);
    $services->alias(QuizFactoryInterface::class, QuizFactory::class);
};
