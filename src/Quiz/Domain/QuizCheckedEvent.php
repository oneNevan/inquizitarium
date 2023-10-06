<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use App\Quiz\Domain\CheckedQuiz\Quiz;

final readonly class QuizCheckedEvent
{
    public function __construct(
        private Quiz $quizResult,
    ) {
    }

    /**
     * @psalm-api
     */
    public function getQuizResult(): Quiz
    {
        return $this->quizResult;
    }
}
