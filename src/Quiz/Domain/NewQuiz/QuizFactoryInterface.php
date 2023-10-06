<?php

declare(strict_types=1);

namespace App\Quiz\Domain\NewQuiz;

use App\Quiz\Creator\QuizCreatorExceptionInterface;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

interface QuizFactoryInterface
{
    /**
     * @throws QuizCreatorExceptionInterface
     */
    public function create(QuestionPoolInterface $pool): Quiz;
}
