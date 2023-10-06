<?php

declare(strict_types=1);

namespace App\Math\Domain\Expression;

use App\Math\Domain\Operators\ArithmeticOperator;

final readonly class RandomExpression implements ExpressionInterface
{
    private string $expression;

    public function __construct(
        int $maxOperandValue = 99,
    ) {
        $cases = ArithmeticOperator::cases();
        $this->expression = sprintf(
            '%u %s %u',
            random_int(0, $maxOperandValue),
            $cases[random_int(0, count($cases) - 1)]->value,
            random_int(0, $maxOperandValue),
        );
    }

    public function __toString(): string
    {
        return $this->expression;
    }
}
