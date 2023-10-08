<?php

declare(strict_types=1);

namespace App\Quiz\Creator;

use App\Quiz\Domain\NewQuiz\Quiz;
use App\Quiz\Domain\NewQuiz\QuizFactoryInterface;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\Domain\QuizCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

/**
 * TODO: I want a feature to define what question pool should be used for the new quiz (in runtime!).
 *
 * A way to achieve that:
 *  - each question pool service should be tagged with a custom service tag (default, random, etc..).
 *  - handler should be able to handle multiple different commands, i.e.
 *      CreateQuiz -> using 'default' question pool (some static pool stored in database)
 *      CreateRandomQuiz -> using 'random' question pool (generated in runtime)
 *      CreateExternalQuiz -> using 'external' question pool (from a 3rd party pool provider :) )
 *      etc..
 *  - handler takes a question pool (for given command) from a tagged service locator and passes it to the factory
 *  - client (application using Quiz Creator service) provides a command required for their use case
 */
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
