<?php

declare(strict_types=1);

namespace App\Quiz\Domain\NewQuiz;

use App\Quiz\Domain\QuestionPool\Question;
use Ramsey\Uuid\UuidInterface;

final readonly class Quiz
{
    /**
     * @param non-empty-list<Question> $questions
     */
    public function __construct(
        private UuidInterface $id,
        private array $questions,
    ) {
    }

    /**
     * @psalm-api
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @psalm-api
     *
     * @return non-empty-list<Question>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }
}
