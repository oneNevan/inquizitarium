<?php

declare(strict_types=1);

namespace App\Quiz\Checker;

use App\Quiz\Checker\Policy\QuestionCheckingPolicyInterface;
use App\Quiz\Checker\Policy\QuizAssessmentPolicyInterface;
use App\Quiz\Domain\CheckedQuiz\Quiz;
use App\Quiz\Domain\CheckedQuiz\QuizFactoryInterface;
use App\Quiz\Domain\QuizCheckedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class CheckQuizHandler
{
    public function __construct(
        private QuestionCheckingPolicyInterface $questionCheckingPolicy,
        private QuizAssessmentPolicyInterface $quizAssessmentPolicy,
        private QuizFactoryInterface $factory,
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(CheckQuiz $command): Quiz
    {
        $checkedQuestions = array_map($this->questionCheckingPolicy->check(...), $command->getQuestions());

        $checkedQuiz = $this->factory->create(
            $command->getQuizId(),
            $checkedQuestions,
            isPassed: $this->quizAssessmentPolicy->isPassed($checkedQuestions),
        );

        $this->eventBus->dispatch(new QuizCheckedEvent($checkedQuiz), [
            new DispatchAfterCurrentBusStamp(),
        ]);

        return $checkedQuiz;
    }
}
