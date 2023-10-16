<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Application;

use App\Core\Application\EventBusInterface;
use App\Quiz\Creator\Domain\Entity\Quiz;
use App\Quiz\Creator\Domain\Factory\QuizFactory;
use App\Quiz\Creator\Domain\QuizCreatedEvent;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;

/**
 * TODO: A feature to define in runtime what question pool should be used for the new quiz would be nice to have.
 *
 * A way to achieve that:
 *  - each question pool service should be tagged with a custom service tag, i.e.:
 *      - 'default' (some static question pool stored in database)
 *      - 'random' (question pool generated in runtime)
 *      - 'external' (question pool from a 3rd party provider :) )
 *      - etc..
 *  - the question pools tag index could be exposed as an optional attribute of CreateQuiz command
 *  - handler takes a question pool (for given command) from a tagged service locator and passes it to the factory
 *  - client (application using Quiz Creator service) provides the index required for their use case
 */
final readonly class CreateQuizHandler
{
    public function __construct(
        private QuestionPoolInterface $questionPool,
        private QuizFactory $factory,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateQuiz $command): Quiz
    {
        $newQuiz = $this->factory->create($this->questionPool, $command->getQuestionsCount());

        $this->eventBus->dispatch(new QuizCreatedEvent($newQuiz));

        return $newQuiz;
    }
}
