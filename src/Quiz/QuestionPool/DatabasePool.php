<?php

declare(strict_types=1);

namespace App\Quiz\QuestionPool;

use App\Math\Domain\Expression\Expression;
use App\Quiz\Domain\QuestionPool\Question;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\QuestionPool\Orm\QuestionRepository;

final readonly class DatabasePool implements QuestionPoolInterface
{
    public function __construct(
        private QuestionRepository $repository,
        private bool $shuffle = true,
    ) {
    }

    public function getQuestions(): iterable
    {
        // TODO: custom query to get questions in random order
        //  like that: select * from quiz_question_pool order by RANDOM();
        //   - https://www.commandprompt.com/education/postgresql-order-by-random/
        //   - https://badtry.net/doctrine-rand-symfony-4-slim-doctrine-random-order-by/
        //   - https://symfony.com/doc/current/doctrine/custom_dql_functions.html
        foreach ($this->repository->findAll() as $question) {
            $answers = $question->getAnswerOptions();
            if ($this->shuffle) {
                // it's fine to shuffle few answers after getting it from the database, no performance concern here...
                shuffle($answers);
            }

            yield new Question(
                new Expression($question->getExpression()),
                $question->getComparison(),
                array_map(static fn (string $expr) => new Expression($expr), $answers),
            );
        }
    }
}
