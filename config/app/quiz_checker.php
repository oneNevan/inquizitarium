<?php

declare(strict_types=1);

use App\Core\Infrastructure\Symfony\Messenger\CommandBus;
use App\Quiz\Checker\Application\CheckQuizHandler;
use App\Quiz\Checker\Domain\Factory\CheckedQuizFactory;
use App\Quiz\Checker\Domain\Service\FuzzyQuestionChecking;
use App\Quiz\Checker\Domain\Service\PassedWhenAllAnswersCorrect;
use App\Quiz\Checker\Domain\Service\QuestionCheckingPolicyInterface;
use App\Quiz\Checker\Domain\Service\QuizAssessmentPolicyInterface;
use App\Quiz\Checker\Infrastructure\ApiController;
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

    $services->set(CheckQuizHandler::class)->tag('messenger.message_handler', [
        'bus' => CommandBus::BUS_NAME,
    ]);

    $services->set(ApiController::class)->tag('controller.service_arguments');
};
