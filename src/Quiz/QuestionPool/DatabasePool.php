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
        private bool $shuffle = false,
    ) {
    }

    public function getQuestions(int $limit = null): iterable
    {
        foreach ($this->shuffle ? $this->repository->getRandom($limit) : $this->repository->getAll($limit) as $question) {
            $answers = $question->getAnswerOptions();
            if ($this->shuffle) {
                // it's fine to shuffle few answers after getting from the database, no performance concern here...
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
