<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\Service;

use App\Quiz\Checker\Domain\ValueObject\CheckedQuestion;

interface QuizAssessmentPolicyInterface
{
    /**
     * @param non-empty-list<CheckedQuestion> $checkedQuestions
     */
    public function isPassed(array $checkedQuestions): bool;
}
