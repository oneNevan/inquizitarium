<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

use App\Quiz\Domain\CheckedQuiz\CheckedQuestion;
use App\Quiz\Domain\SolvedQuiz\AnsweredQuestion;

interface QuestionCheckingPolicyInterface
{
    public function check(AnsweredQuestion $question): CheckedQuestion;
}
