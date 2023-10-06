<?php

declare(strict_types=1);

namespace App\Math\Evaluator;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Value\ValueInterface;

interface EvaluatorInterface
{
    public function evaluate(ExpressionInterface $expression): ValueInterface;
}
