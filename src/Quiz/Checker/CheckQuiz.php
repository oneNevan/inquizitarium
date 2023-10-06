<?php

declare(strict_types=1);

namespace App\Quiz\Checker;

use App\Quiz\Domain\SolvedQuiz\AnsweredQuestion;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Cascade]
final readonly class CheckQuiz
{
    /**
     * @psalm-api
     *
     * @param non-empty-list<AnsweredQuestion> $questions
     */
    public function __construct(
        private UuidInterface $quizId,
        #[Assert\NotBlank]
        private array $questions,
    ) {
    }

    public function getQuizId(): UuidInterface
    {
        return $this->quizId;
    }

    /**
     * @return non-empty-list<AnsweredQuestion>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }
}
