<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\ValueObject;

use App\Math\Domain\ValueObject\Expression;

final readonly class CheckedAnswer
{
    /**
     * @param bool|null $isCorrect null if expression was not selected and checked,
     *                             so it's unknown whether the answer is correct or not
     */
    public function __construct(
        private Expression $expression,
        private ?bool $isCorrect,
    ) {
    }

    public function getExpression(): Expression
    {
        return $this->expression;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }
}
