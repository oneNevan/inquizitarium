<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain;

use App\Quiz\Checker\Domain\Entity\CheckedQuiz;

final readonly class QuizCheckedEvent
{
    public function __construct(
        private CheckedQuiz $quiz,
    ) {
    }

    public function getQuiz(): CheckedQuiz
    {
        return $this->quiz;
    }
}
