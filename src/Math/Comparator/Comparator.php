<?php

declare(strict_types=1);

namespace App\Math\Comparator;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Math\Evaluator\EvaluatorInterface;
use App\Math\Evaluator\IntEvaluator;

final readonly class Comparator implements ComparatorInterface
{
    public function __construct(
        private EvaluatorInterface $evaluator = new IntEvaluator(),
    ) {
    }

    /**
     * TODO: supports only IntValue and ComparisonOperator::Equal, but it works for now..
     *
     * @psalm-suppress NoValue as new comparison operator could be added later.
     */
    public function compare(ExpressionInterface $a, ComparisonOperator $operator, ExpressionInterface $b): bool
    {
        if (ComparisonOperator::Equal !== $operator) {
            throw new UnsupportedOperatorException($operator);
        }

        return $this->evaluator->evaluate($a)->getValue() === $this->evaluator->evaluate($b)->getValue();
    }
}
