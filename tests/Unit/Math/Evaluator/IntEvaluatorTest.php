<?php

declare(strict_types=1);

namespace App\Tests\Unit\Math\Evaluator;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Operators\ArithmeticOperator;
use App\Math\Evaluator\IntEvaluator;
use PHPUnit\Framework\TestCase;

class IntEvaluatorTest extends TestCase
{
    /**
     * @dataProvider validExpressionProvider
     */
    public function testEvaluate(string $expr, int $expectedResult): void
    {
        $this->assertSame($expectedResult, (new IntEvaluator())->evaluate(new Expression($expr))->getValue());
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
        $this->expectExceptionObject(new \InvalidArgumentException("Unable to evaluate expression '$expr'"));
        (new IntEvaluator())->evaluate(new Expression($expr));
    }

    public function invalidExpressionProvider(): iterable
    {
        return [
            ['1 + b'],
            ['2 - 1'],
            ['4 / 2'],
            ['1 * 5'],
        ];
    }
}
