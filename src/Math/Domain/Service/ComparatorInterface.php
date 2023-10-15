<?php

declare(strict_types=1);

namespace App\Math\Domain\Service;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;

interface ComparatorInterface
{
    public function compare(Expression $a, ComparisonOperator $operator, Expression $b): bool;
}
