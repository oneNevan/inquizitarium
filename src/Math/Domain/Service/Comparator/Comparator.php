<?php

declare(strict_types=1);

namespace App\Math\Domain\Service\Comparator;

use App\Math\Domain\Service\ComparatorInterface;
use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;

final readonly class Comparator implements ComparatorInterface
{
    /**
     * TODO: supports only ComparisonOperator::Equal, but it works for now..
     *
     * @psalm-suppress NoValue (new comparison operator could be added later).
     */
    public function compare(Expression $a, ComparisonOperator $operator, Expression $b): bool
    {
        if (ComparisonOperator::Equal !== $operator) {
            throw new UnsupportedOperatorException($operator);
        }

        return $a->evaluate()->getValue() === $b->evaluate()->getValue();
    }
}
