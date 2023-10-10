<?php

declare(strict_types=1);

namespace App\Quiz\Domain\NewQuiz;

use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

interface QuizFactoryInterface
{
    /**
     * @param positive-int|null $questionsLimit
     */
    public function create(QuestionPoolInterface $pool, int $questionsLimit = null): Quiz;
}
