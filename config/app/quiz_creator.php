<?php

declare(strict_types=1);

use App\Quiz\Creator\CreateQuizHandler;
use App\Quiz\Creator\QuestionPool\InMemoryQuestionPool;
use App\Quiz\Creator\QuizFactory;
use App\Quiz\Domain\NewQuiz\QuizFactoryInterface;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CreateQuizHandler::class)
        ->tag('messenger.message_handler');

    $services->set(QuizFactory::class);
    $services->alias(QuizFactoryInterface::class, QuizFactory::class);

    $services->set(InMemoryQuestionPool::class);
    $services->alias(QuestionPoolInterface::class, InMemoryQuestionPool::class);
};
