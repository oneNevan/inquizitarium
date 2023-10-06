<?php

declare(strict_types=1);

namespace App\Math\Comparator;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;

interface ComparatorInterface
{
    public function compare(ExpressionInterface $a, ComparisonOperator $operator, ExpressionInterface $b): bool;
}
