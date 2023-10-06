<?php

declare(strict_types=1);

use App\Quiz\Checker\CheckedQuizFactory;
use App\Quiz\Checker\CheckQuizHandler;
use App\Quiz\Checker\Policy\FuzzyQuestionChecking;
use App\Quiz\Checker\Policy\PassedWhenAllAnswersCorrect;
use App\Quiz\Checker\Policy\QuestionCheckingPolicyInterface;
use App\Quiz\Checker\Policy\QuizAssessmentPolicyInterface;
use App\Quiz\Domain\CheckedQuiz\QuizFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(FuzzyQuestionChecking::class);
    $services->alias(QuestionCheckingPolicyInterface::class, FuzzyQuestionChecking::class);

    $services->set(PassedWhenAllAnswersCorrect::class);
    $services->alias(QuizAssessmentPolicyInterface::class, PassedWhenAllAnswersCorrect::class);

    $services->set(CheckedQuizFactory::class);
    $services->alias(QuizFactoryInterface::class, CheckedQuizFactory::class);

    $services->set(CheckQuizHandler::class)
        ->tag('messenger.message_handler');
};
