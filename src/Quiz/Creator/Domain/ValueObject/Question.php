<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain\ValueObject;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;

final readonly class Question
{
    /**
     * @param non-empty-list<Expression> $answerOptions
     */
    public function __construct(
        private Expression $expression,
        private ComparisonOperator $comparisonOperator,
        private array $answerOptions,
    ) {
    }

    public function getExpression(): Expression
    {
        return $this->expression;
    }

    public function getComparisonOperator(): ComparisonOperator
    {
        return $this->comparisonOperator;
    }

    /**
     * @return non-empty-list<Expression>
     */
    public function getAnswerOptions(): array
    {
        return $this->answerOptions;
    }
}
