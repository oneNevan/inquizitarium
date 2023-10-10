<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\QuestionPool;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\QuestionPool\FallbackPool;
use App\Quiz\QuestionPool\Orm\QuestionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabasePoolTest extends KernelTestCase
{
    public function testQuestionPool(): void
    {
        /** @var QuestionPoolInterface $questionsPool */
        $questionsPool = static::getContainer()->get(QuestionPoolInterface::class);
        // making sure that decorator is applied
        $this->assertInstanceOf(FallbackPool::class, $questionsPool);

        $pool = [];
        foreach ($questionsPool->getQuestions() as $question) {
            $this->assertSame(ComparisonOperator::Equal, $question->getComparisonOperator());
            $pool[(string) $question->getExpression()] = array_map(
                static fn (Expression $expr) => (string) $expr,
                $question->getAnswerOptions(),
            );
        }

        // using refleciton to avoid making the constant public just for testing purposes...
        $defaultPool = (new \ReflectionClassConstant(QuestionFixture::class, 'POOL'))->getValue();
        $this->assertIsArray($defaultPool);
        $this->assertNotEquals(
            array_keys($defaultPool),
            array_keys($pool),
            'Failed asserting that questions are shuffled',
        );

        // sorting both arrays to check answers are shuffled
        $sort = static fn (array &$answers) => sort($answers, \SORT_STRING);
        ksort($pool, \SORT_STRING);
        ksort($defaultPool, \SORT_STRING);
        $this->assertNotEquals(
            array_values($defaultPool),
            array_values($pool),
            'Failed asserting that answers are shuffled',
        );

        // sorting shuffled answers to check if sorted pools are actually equal
        array_walk($pool, $sort);
        array_walk($defaultPool, $sort);
        $this->assertEquals(
            $defaultPool,
            $pool,
            'Failed asserting that test pool matches the default pool',
        );
    }
}
