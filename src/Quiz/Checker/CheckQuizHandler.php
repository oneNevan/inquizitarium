<?php

declare(strict_types=1);

namespace App\Quiz\Checker;

use App\Quiz\Checker\Policy\QuestionCheckingPolicyInterface;
use App\Quiz\Checker\Policy\QuizPassingPolicyInterface;
use App\Quiz\Domain\QuizCheckedEvent;
use App\Quiz\Domain\QuizResult\Result;
use App\Quiz\Domain\QuizResult\ResultFactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class CheckQuizHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private QuestionCheckingPolicyInterface $questionCheckingPolicy,
        private QuizPassingPolicyInterface $quizPassingPolicy,
        private ResultFactoryInterface $resultFactory,
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(CheckQuiz $command): Result
    {
        $checkedQuestions = array_map($this->questionCheckingPolicy->check(...), $command->getQuestions());

        $quizResult = $this->resultFactory->create(
            $command->getQuizId(),
            $checkedQuestions,
            isPassed: $this->quizPassingPolicy->isPassed($checkedQuestions),
        );

        $this->eventBus->dispatch(new QuizCheckedEvent($quizResult), [
            new DispatchAfterCurrentBusStamp(),
        ]);

        return $quizResult;
    }
}
