<?php

declare(strict_types=1);

use App\Quiz\Checker\CheckQuizHandler;
use App\Quiz\Checker\Policy\FuzzyQuestionChecking;
use App\Quiz\Checker\Policy\PassingWhenAllAnswersCorrect;
use App\Quiz\Checker\Policy\QuestionCheckingPolicyInterface;
use App\Quiz\Checker\Policy\QuizPassingPolicyInterface;
use App\Quiz\Checker\ResultFactory;
use App\Quiz\Domain\QuizResult\ResultFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(FuzzyQuestionChecking::class);
    $services->alias(QuestionCheckingPolicyInterface::class, FuzzyQuestionChecking::class);

    $services->set(PassingWhenAllAnswersCorrect::class);
    $services->alias(QuizPassingPolicyInterface::class, PassingWhenAllAnswersCorrect::class);

    $services->set(ResultFactory::class);
    $services->alias(ResultFactoryInterface::class, ResultFactory::class);

    $services->set(CheckQuizHandler::class)
        ->tag('messenger.message_handler');
};
