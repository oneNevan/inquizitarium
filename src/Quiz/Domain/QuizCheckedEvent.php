<?php

declare(strict_types=1);

namespace App\Quiz\Domain;

use App\Quiz\Domain\QuizResult\Result;

final readonly class QuizCheckedEvent
{
    public function __construct(
        private Result $quizResult,
    ) {
    }

    /**
     * @psalm-api
     */
    public function getQuizResult(): Result
    {
        return $this->quizResult;
    }
}
