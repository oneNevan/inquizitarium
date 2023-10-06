<?php

declare(strict_types=1);

namespace App\Math\Evaluator;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Value\IntValue;
use App\Math\Domain\Value\ValueInterface;

final readonly class IntEvaluator implements EvaluatorInterface
{
    /**
     * TODO: there is likely to be a better way than preg_match, but it works for now..
     */
    public function evaluate(ExpressionInterface $expression): ValueInterface
    {
        $expr = trim((string) $expression);
        if (preg_match('/^\d+$/', $expr)) {
            return new IntValue((int) $expr);
        }

        if (preg_match('/^(\d+)\s*\+\s*(\d+)$/', $expr, $matches)) {
            return new IntValue(((int) $matches[1]) + ((int) $matches[2]));
        }

        throw new \InvalidArgumentException("Unable to evaluate expression '$expr'");
    }
}
