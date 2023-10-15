<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\Service;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;

final class QuestionHasNoSelectedAnswersException extends \DomainException
{
    public function __construct(
        Expression $question,
        ComparisonOperator $comparisonOperator,
    ) {
        parent::__construct(sprintf(
            'Question "%s %s ?" has no selected answers',
            $question->__toString(),
            $comparisonOperator->value,
        ));
    }
}
