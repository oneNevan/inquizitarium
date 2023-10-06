<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Policy;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Quiz\Checker\QuizCheckerExceptionInterface;

final class QuestionHasNoSelectedAnswersException extends \Exception implements QuizCheckerExceptionInterface
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
