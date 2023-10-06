<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuizResult;

use App\Math\Domain\Expression\ExpressionInterface;

final readonly class CheckedAnswer
{
    /**
     * @param bool|null $isCorrect null if expression was not selected and checked,
     *                             so it's unknown whether the answer is correct or not
     */
    public function __construct(
        private ExpressionInterface $expression,
        private ?bool $isCorrect = null,
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
    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }
}
