<?php

declare(strict_types=1);

namespace App\Quiz\Domain\SolvedQuiz;

use App\Math\Domain\Expression\ExpressionInterface;

final readonly class AnswerOption
{
    /**
     * @psalm-api
     */
    public function __construct(
        private ExpressionInterface $expression,
        private bool $isSelected,
    ) {
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }
}
