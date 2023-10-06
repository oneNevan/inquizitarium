<?php

declare(strict_types=1);

namespace App\Math\Domain\Expression;

/**
 * Default implementation for expression interface.
 */
final readonly class Expression implements ExpressionInterface
{
    public function __construct(
        private string $expression,
    ) {
    }

    public function __toString(): string
    {
        return $this->expression;
    }
}
