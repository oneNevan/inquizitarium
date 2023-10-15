<?php

declare(strict_types=1);

namespace App\Math\Domain\ValueObject;

use App\Math\Domain\ValueObject\Operator\ArithmeticOperator;
use App\Math\Domain\ValueObject\Value\IntValue;
use App\Math\Domain\ValueObject\Value\ValueInterface;

final readonly class Expression implements \Stringable
{
    private const NATURAL_NUMBER = '/^\d+$/';
    private const SUM_OF_TWO_NUMBERS = '/^(\d+)\s*\+\s*(\d+)$/';

    private function __construct(
        private string $expr,
    ) {
    }

    public function __toString(): string
    {
        return $this->expr;
    }

    public function evaluate(): ValueInterface
    {
        if (preg_match(self::NATURAL_NUMBER, $this->expr)) {
            return new IntValue((int) $this->expr);
        }

        if (preg_match(self::SUM_OF_TWO_NUMBERS, $this->expr, $matches)) {
            return new IntValue(((int) $matches[1]) + ((int) $matches[2]));
        }

        throw new \LogicException("Unable to evaluate expression '$this->expr'.");
    }

    /**
     * TODO: support more valid expressions?
     *
     * For now only the following expressions are supported:
     *  - \d+ (threat any natural number as an expression, ex: 1,12,42,....N)
     *  - ^\d+\s*\+\s*\d+$ (a sum of two natural numbers, ex: '1 + 2', '2+2')
     */
    public static function new(string $expr): self
    {
        $expr = trim($expr);

        if (preg_match(self::NATURAL_NUMBER, $expr)) {
            return new self($expr);
        }

        if (preg_match(self::SUM_OF_TWO_NUMBERS, $expr, $matches)) {
            return new self(sprintf(
                '%u %s %u',
                $matches[1],
                ArithmeticOperator::Addition->value,
                $matches[2],
            ));
        }

        throw new \InvalidArgumentException("Given string '$expr' is not a valid math expression");
    }

    public static function random(int $maxOperandValue = 99): self
    {
        $cases = ArithmeticOperator::cases();

        return new self(sprintf(
            '%u %s %u',
            random_int(0, $maxOperandValue),
            $cases[random_int(0, count($cases) - 1)]->value,
            random_int(0, $maxOperandValue),
        ));
    }
}
