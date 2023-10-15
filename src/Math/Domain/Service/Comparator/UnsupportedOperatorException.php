<?php

declare(strict_types=1);

namespace App\Math\Domain\Service\Comparator;

use App\Math\Domain\ValueObject\Operator\ComparisonOperator;

final class UnsupportedOperatorException extends \InvalidArgumentException
{
    public function __construct(ComparisonOperator $operator)
    {
        parent::__construct("Comparison operator '$operator->value' is not supported.");
    }
}
