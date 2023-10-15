<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain;

use App\Quiz\Creator\Domain\Entity\Quiz;

final readonly class QuizCreatedEvent
{
    public function __construct(
        private Quiz $quiz,
    ) {
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }
}
