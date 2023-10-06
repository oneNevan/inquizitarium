<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

class PassingWhenAllAnswersCorrect implements QuizPassingPolicyInterface
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
