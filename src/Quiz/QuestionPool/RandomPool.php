<?php

declare(strict_types=1);

namespace App\Quiz\QuestionPool;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Expression\RandomExpression;
use App\Math\Domain\Operators\ArithmeticOperator;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Quiz\Domain\QuestionPool\Question;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

/**
 * Generates random questions with at least 1 correct option for all questions.
 */
final readonly class RandomPool implements QuestionPoolInterface
{
    public function __construct(
        private int $poolSize = 100,
        private int $maxAnswerOptions = 5,
        private int $maxOperandValue = 10,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getQuestions(int $limit = null): iterable
    {
        return new \ArrayIterator(iterator_to_array($this->getIterator($limit)));
    }

    /**
     * @return \Generator<Question>
     *
     * @throws \Exception if random_int(...) fails
     */
    private function getIterator(int $limit = null): \Generator
    {
        $i = null === $limit ? $this->poolSize : min($limit, $this->poolSize);
        while ($i-- > 0) {
            $a = random_int(0, $this->maxOperandValue);
            $b = random_int(0, $this->maxOperandValue);
            $answers = [
                new Expression((string) ($a + $b)), // indeed correct answer (at least one required)
                ...$this->createRandomAnswers(count: random_int(1, $this->maxAnswerOptions - 1)),
            ];
            shuffle($answers); // so that the correct answer is not always first

            yield new Question(
                new Expression(sprintf('%u %s %u', $a, ArithmeticOperator::Addition->value, $b)),
                ComparisonOperator::Equal,
                $answers
            );
        }
    }

    /**
     * TODO: duplicate answer options might get generated.. but for now, it's fine..
     *
     * @param positive-int $count
     *
     * @return list<ExpressionInterface>
     *
     * @throws \Exception if random_int(...) fails
     */
    private function createRandomAnswers(int $count): array
    {
        $arr = [];
        while ($count > 0) {
            $arr[] = random_int(-10, 20) > 0 // just a simple way to randomize options
                ? new RandomExpression($this->maxOperandValue) // expr with two operands (preferred option)
                : new Expression((string) (random_int(0, $this->maxOperandValue) * 2)); // expr with single real number
            --$count;
        }

        return $arr;
    }
}
