<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\Entity;

use App\Quiz\Checker\Domain\ValueObject\CheckedQuestion;
use Ramsey\Uuid\UuidInterface;

final readonly class CheckedQuiz
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

    public function getQuizId(): UuidInterface
    {
        return $this->quizId;
    }

    /**
     * @return non-empty-list<CheckedQuestion>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function isPassed(): bool
    {
        return $this->isPassed;
    }
}
