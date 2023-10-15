<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\QuestionPool;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use App\Quiz\Creator\Infrastructure\Orm\Question;
use App\Quiz\Creator\Infrastructure\Orm\QuestionRepository;
use App\Quiz\Creator\Infrastructure\Service\DatabasePool;
use PHPUnit\Framework\TestCase;

class DatabasePoolTest extends TestCase
{
    /**
     * @testWith [true]
     *           [false]
     */
    public function testGetQuestions(bool $shuffle): void
    {
        $questions = [
            (new Question())
                ->setExpression('1 + 1')
                ->setComparison(ComparisonOperator::Equal)
                ->setAnswerOptions(['1 + 1', '2 + 2', '3 + 3', '4 + 4']),
            (new Question())
                ->setExpression('2 + 2')
                ->setComparison(ComparisonOperator::Equal)
                ->setAnswerOptions(['1 + 2', '2 + 3', '3 + 4', '4 + 5']),
        ];
        $repository = $this->createMock(QuestionRepository::class);
        $repository->expects($this->once())
            ->method($shuffle ? 'getRandom' : 'getAll')
            ->with(42)
            ->willReturn($questions);

        $pool = new DatabasePool($repository, shuffle: $shuffle);

        $cnt = 0;
        foreach ($pool->getQuestions(limit: 42) as $i => $question) {
            $answersAfter = array_map(static fn (Expression $expr) => (string) $expr, $question->getAnswerOptions());
            $answersBefore = $questions[$i]->getAnswerOptions();
            if ($shuffle) {
                // when shuffling enabled, arrays should not match originally, but should match after sorting
                $this->assertNotEquals($answersAfter, $answersBefore);
                sort($answersAfter, \SORT_STRING);
                sort($answersBefore, \SORT_STRING);
            }
            $this->assertEquals($answersAfter, $answersBefore);
            ++$cnt;
        }

        $this->assertCount($cnt, $questions);
    }
}
