<?php

declare(strict_types=1);

namespace App\Tests\Unit\Math\Domain\Service;

use App\Math\Domain\Service\Comparator\Comparator;
use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use PHPUnit\Framework\TestCase;

class ComparatorTest extends TestCase
{
    /**
     * @dataProvider compareDataProvider
     */
    public function testCompare(string $expr1, string $expr2, bool $isEqual): void
    {
        $result = (new Comparator())->compare(
            Expression::new($expr1),
            ComparisonOperator::Equal,
            Expression::new($expr2),
        );

        $this->assertSame($isEqual, $result);
    }

    public function compareDataProvider(): iterable
    {
        return [
            ['0', '0', true],
            ['42', '42', true],
            ['1 + 1', '2 + 0', true],
            ['111 + 222', '333', true],
            ['42', '43', false],
            ['2 + 1', '2 + 0', false],
            ['111 + 111', '333', false],
        ];
    }

    /**
     * If comparison type is not supported - exception will be thrown.
     *
     * @dataProvider allComparisonTypesProvider
     */
    public function testAllComparisonTypesAreSupported(ComparisonOperator $comparisonType): void
    {
        $this->expectNotToPerformAssertions();
        (new Comparator())->compare(Expression::new('1'), $comparisonType, Expression::new('2'));
    }

    public function allComparisonTypesProvider(): iterable
    {
        foreach (ComparisonOperator::cases() as $comparisonType) {
            yield [$comparisonType];
        }
    }
}
