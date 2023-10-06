<?php

declare(strict_types=1);

namespace App\Tests\Unit\Math\Comparator;

use App\Math\Comparator\Comparator;
use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Operators\ComparisonOperator;
use PHPUnit\Framework\TestCase;

class ComparatorTest extends TestCase
{
    /**
     * @dataProvider compareDataProvider
     */
    public function testCompare(string $expr1, string $expr2, bool $isEqual): void
    {
        $result = (new Comparator())->compare(
            new Expression($expr1),
            ComparisonOperator::Equal,
            new Expression($expr2),
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
        (new Comparator())->compare(new Expression('1'), $comparisonType, new Expression('2'));
    }

    public function allComparisonTypesProvider(): iterable
    {
        foreach (ComparisonOperator::cases() as $comparisonType) {
            yield [$comparisonType];
        }
    }
}
