<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

use App\Quiz\Domain\QuizResult\CheckedQuestion;

interface QuizPassingPolicyInterface
{
    /**
     * @param non-empty-list<CheckedQuestion> $checkedQuestions
     */
    public function isPassed(array $checkedQuestions): bool;
}
