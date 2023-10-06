<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

use App\Math\Comparator\Comparator;
use App\Math\Comparator\ComparatorInterface;
use App\Quiz\Domain\CheckedQuiz\CheckedAnswer;
use App\Quiz\Domain\CheckedQuiz\CheckedQuestion;
use App\Quiz\Domain\SolvedQuiz\AnsweredQuestion;

/**
 * Fuzzy question checking policy implementation.
 *
 * To accept answer for a question fuzzy checking requires the following conditions to be met:
 *  - at least one valid answer should be selected
 *  - none invalid answers should be selected
 */
final readonly class FuzzyQuestionChecking implements QuestionCheckingPolicyInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private ComparatorInterface $expressionComparator = new Comparator(),
    ) {
    }

    public function check(AnsweredQuestion $question): CheckedQuestion
    {
        $correctCnt = $wrongCnt = 0;
        $checkedAnswers = [];
        foreach ($question->getAnswers() as $answer) {
            // fuzzy checking does not require all correct answers to be selected
            // so there is no need to evaluate answers that are not selected
            $isCorrect = !$answer->isSelected() ? null : $this->expressionComparator->compare(
                $question->getExpression(),
                $question->getComparisonOperator(),
                $answer->getExpression(),
            );
            $checkedAnswers[] = new CheckedAnswer($answer->getExpression(), $isCorrect);
            if (true === $isCorrect) {
                ++$correctCnt;
            } elseif (false === $isCorrect) {
                ++$wrongCnt;
            }
        }

        if (0 === $correctCnt && 0 === $wrongCnt) {
            throw new QuestionHasNoSelectedAnswersException($question->getExpression(), $question->getComparisonOperator());
        }

        return new CheckedQuestion(
            $question->getExpression(),
            $question->getComparisonOperator(),
            $checkedAnswers,
            isAnswerCorrect: 0 === $wrongCnt && $correctCnt > 0,
        );
    }
}
