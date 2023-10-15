<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Infrastructure\Service;

use App\Math\Domain\ValueObject\Expression;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;
use App\Quiz\Creator\Domain\ValueObject\Question;
use App\Quiz\Creator\Infrastructure\Orm\QuestionRepository;

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
                Expression::new($question->getExpression()),
                $question->getComparison(),
                array_map(static fn (string $expr) => Expression::new($expr), $answers),
            );
        }
    }
}
