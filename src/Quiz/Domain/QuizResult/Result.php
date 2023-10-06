<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuizResult;

use Ramsey\Uuid\UuidInterface;

final readonly class Result
{
    /**
     * @param non-empty-list<CheckedQuestion> $questions
     */
    public function __construct(
        private UuidInterface $quizId,
        private array $questions,
        private bool $isPassed,
    ) {
    }

    /**
     * @psalm-api
     */
    public function getQuizId(): UuidInterface
    {
        return $this->quizId;
    }

    /**
     * @psalm-api
     *
     * @return non-empty-list<CheckedQuestion>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * @psalm-api
     */
    public function isPassed(): bool
    {
        return $this->isPassed;
    }
}
