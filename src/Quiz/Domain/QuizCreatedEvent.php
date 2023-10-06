<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use App\Quiz\Domain\NewQuiz\Quiz;

final readonly class QuizCreatedEvent
{
    public function __construct(
        private Quiz $quiz,
    ) {
    }

    /**
     * @psalm-api
     */
    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }
}
