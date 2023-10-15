<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain\Entity;

use App\Quiz\Creator\Domain\ValueObject\Question;
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

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return non-empty-list<Question>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }
}
