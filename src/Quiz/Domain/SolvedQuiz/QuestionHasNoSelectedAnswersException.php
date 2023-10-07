<?php

declare(strict_types=1);

namespace App\Quiz\Domain\SolvedQuiz;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;

final class QuestionHasNoSelectedAnswersException extends \DomainException
{
    public function __construct(
        ExpressionInterface $question,
        ComparisonOperator $comparisonOperator,
    ) {
        parent::__construct(sprintf(
            'Question "%s %s ?" has no selected answers',
            $question->__toString(),
            $comparisonOperator->value,
        ));
    }
}
