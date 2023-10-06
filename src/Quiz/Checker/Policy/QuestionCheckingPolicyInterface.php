<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

use App\Quiz\Checker\QuizCheckerExceptionInterface;
use App\Quiz\Domain\QuizResult\CheckedQuestion;
use App\Quiz\Domain\SolvedQuiz\AnsweredQuestion;

interface QuestionCheckingPolicyInterface
{
    /**
     * @throws QuizCheckerExceptionInterface
     */
    public function check(AnsweredQuestion $question): CheckedQuestion;
}
