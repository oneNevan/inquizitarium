<?php

declare(strict_types=1);

namespace App\Math\Domain\ValueObject\Value;

final readonly class IntValue implements ValueInterface
{
    public function __construct(
        private int $value,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
