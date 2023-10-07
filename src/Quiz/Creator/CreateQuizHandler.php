<?php

declare(strict_types=1);

namespace App\Quiz\Creator;

use App\Quiz\Domain\NewQuiz\Quiz;
use App\Quiz\Domain\NewQuiz\QuizFactoryInterface;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\Domain\QuizCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class CreateQuizHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private QuestionPoolInterface $questionPool,
        private QuizFactoryInterface $factory,
        private MessageBusInterface $eventBus,
    ) {
    }

    /**
     * @psalm-suppress UnusedParam
     */
    public function __invoke(CreateQuiz $command): Quiz
    {
        $newQuiz = $this->factory->create($this->questionPool);

        $this->eventBus->dispatch(new QuizCreatedEvent($newQuiz), [
            new DispatchAfterCurrentBusStamp(),
        ]);

        return $newQuiz;
    }
}
