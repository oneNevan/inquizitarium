<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\ValueObject;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;

final readonly class CheckedQuestion
{
    /**
     * @param non-empty-list<CheckedAnswer> $answers
     */
    public function __construct(
        private Expression $expression,
        private ComparisonOperator $comparisonOperator,
        private array $answers,
        private bool $isAnswerCorrect,
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
     * @return non-empty-list<CheckedAnswer>
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function isAnswerCorrect(): bool
    {
        return $this->isAnswerCorrect;
    }
}
