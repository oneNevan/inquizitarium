<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuestionPool;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;

final readonly class Question
{
    /**
     * @param non-empty-list<ExpressionInterface> $answerOptions
     */
    public function __construct(
        private ExpressionInterface $expression,
        private ComparisonOperator $comparisonOperator,
        private array $answerOptions,
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
     * @return non-empty-list<ExpressionInterface>
     */
    public function getAnswerOptions(): array
    {
        return $this->answerOptions;
    }
}
