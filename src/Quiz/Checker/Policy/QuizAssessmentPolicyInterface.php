<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

use App\Quiz\Domain\CheckedQuiz\CheckedQuestion;

interface QuizAssessmentPolicyInterface
{
    /**
     * @param non-empty-list<CheckedQuestion> $checkedQuestions
     */
    public function isPassed(array $checkedQuestions): bool;
}
