<?php

declare(strict_types=1);

namespace App\Quiz\Domain\NewQuiz;

use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

interface QuizFactoryInterface
{
    public function create(QuestionPoolInterface $pool): Quiz;
}
