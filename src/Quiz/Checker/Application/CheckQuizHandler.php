<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Application;

use App\Core\Application\EventBusInterface;
use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use App\Quiz\Checker\Domain\Factory\CheckedQuizFactory;
use App\Quiz\Checker\Domain\QuizCheckedEvent;
use App\Quiz\Checker\Domain\Service\QuestionCheckingPolicyInterface;
use App\Quiz\Checker\Domain\Service\QuizAssessmentPolicyInterface;

final readonly class CheckQuizHandler
{
    public function __construct(
        private QuestionCheckingPolicyInterface $questionCheckingPolicy,
        private QuizAssessmentPolicyInterface $quizAssessmentPolicy,
        private CheckedQuizFactory $factory,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CheckQuiz $command): CheckedQuiz
    {
        $checkedQuestions = array_map($this->questionCheckingPolicy->check(...), $command->getQuestions());

        $checkedQuiz = $this->factory->create(
            $command->getQuizId(),
            $checkedQuestions,
            isPassed: $this->quizAssessmentPolicy->isPassed($checkedQuestions),
        );

        $this->eventBus->dispatch(new QuizCheckedEvent($checkedQuiz));

        return $checkedQuiz;
    }
}
