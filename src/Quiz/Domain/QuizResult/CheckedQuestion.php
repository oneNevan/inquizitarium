<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuizResult;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;

final readonly class CheckedQuestion
{
    /**
     * @param non-empty-list<CheckedAnswer> $answers
     */
    public function __construct(
        private ExpressionInterface $expression,
        private ComparisonOperator $comparisonOperator,
        private array $answers,
        private bool $isAnswerCorrect,
    ) {
    }

    /**
     * @psalm-api
     */
    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }

    /**
     * @psalm-api
     */
    public function getComparisonOperator(): ComparisonOperator
    {
        return $this->comparisonOperator;
    }

    /**
     * @psalm-api
     *
     * @return non-empty-list<CheckedAnswer>
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @psalm-api
     */
    public function isAnswerCorrect(): bool
    {
        return $this->isAnswerCorrect;
    }
}
