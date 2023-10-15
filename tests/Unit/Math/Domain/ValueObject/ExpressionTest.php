<?php

declare(strict_types=1);

namespace App\Tests\Unit\Math\Domain\ValueObject;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ArithmeticOperator;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    /**
     * @dataProvider toStringDataProvider
     */
    public function testToString(string $expectedString, string $expr): void
    {
        $this->assertSame($expectedString, (string) Expression::new($expr));
    }

    public function toStringDataProvider(): iterable
    {
        return [
            ['1 + 1', '1 + 1'],
            ['1 + 1', ' 1 + 1 '],
            ['1 + 1', '1+1'],
        ];
    }

    /**
     * @dataProvider validExpressionProvider
     */
    public function testEvaluate(string $expr, int $expectedResult): void
    {
        $this->assertSame($expectedResult, Expression::new($expr)->evaluate()->getValue());
    }

    public function validExpressionProvider(): iterable
    {
        foreach (range(0, 10) as $int) {
            // any natural number without operators
            $expression = (string) $int;
            yield "expr: $expression" => [$expression, $int];

            foreach (ArithmeticOperator::cases() as $supportedOperator) {
                // test expressions with all supported operators
                $expression = "$int $supportedOperator->value $int";
                yield "expr: $expression" => [$expression, $int + $int];
            }
        }
    }

    /**
     * @dataProvider invalidExpressionProvider
     */
    public function testException(string $expr): void
    {
        $this->expectExceptionObject(new \InvalidArgumentException("Given string '$expr' is not a valid math expression"));
        Expression::new($expr);
    }

    public function invalidExpressionProvider(): iterable
    {
        return [
            ['1 + b'],
            ['2 - 1'],
            ['4 / 2'],
            ['1 * 5'],
            ['foobar'],
        ];
    }
}
