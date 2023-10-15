<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\QuestionPool;

use App\Quiz\Creator\Domain\Service\QuestionPool\FallbackPool;
use App\Quiz\Creator\Domain\Service\QuestionPool\RandomPool;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;
use PHPUnit\Framework\TestCase;

class FallbackPoolTest extends TestCase
{
    public function testFallbackPoolIgnored(): void
    {
        $fallbackPool = $this->createMock(QuestionPoolInterface::class);
        $fallbackPool->expects($this->never())->method('getQuestions');

        $pool = new FallbackPool(
            decoratedPool: new RandomPool(poolSize: 3),
            fallbackPool: $fallbackPool,
        );

        $this->assertCount(3, iterator_to_array($pool->getQuestions()));
        $this->assertCount(2, iterator_to_array($pool->getQuestions(limit: 2)));
    }

    public function testFallbackPoolCalledWhenDecoratedIsEmpty(): void
    {
        $pool = new FallbackPool(
            decoratedPool: new RandomPool(poolSize: 0),
            fallbackPool: new RandomPool(poolSize: 3),
        );

        $this->assertCount(3, iterator_to_array($pool->getQuestions()));
        $this->assertCount(2, iterator_to_array($pool->getQuestions(limit: 2)));
    }
}
