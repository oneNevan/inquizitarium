<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

class PassedWhenAllAnswersCorrect implements QuizAssessmentPolicyInterface
{
    public function isPassed(array $checkedQuestions): bool
    {
        foreach ($checkedQuestions as $question) {
            if (!$question->isAnswerCorrect()) {
                return false;
            }
        }

        return true;
    }
}
