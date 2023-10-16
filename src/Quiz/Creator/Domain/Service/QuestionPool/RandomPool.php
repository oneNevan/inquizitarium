<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain\Service\QuestionPool;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ArithmeticOperator;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;
use App\Quiz\Creator\Domain\ValueObject\Question;

/**
 * Generates random questions with at least 1 correct option for all questions.
 */
final readonly class RandomPool implements QuestionPoolInterface
{
    /**
     * @param positive-int $poolSize
     * @param positive-int $maxAnswerOptions
     * @param positive-int $maxOperandValue
     * @param positive-int $minOperandValue
     */
    public function __construct(
        private int $poolSize = 100,
        private int $maxAnswerOptions = 5,
        private int $maxOperandValue = 10,
        private int $minOperandValue = 1,
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
            $a = random_int($this->minOperandValue, $this->maxOperandValue);
            $b = random_int($this->minOperandValue, $this->maxOperandValue);
            $answers = [
                $this->createCorrectAnswer($a, $b),
                ...$this->createRandomAnswers(count: random_int(1, $this->maxAnswerOptions - 1)),
            ];
            shuffle($answers); // so that the correct answer is not always first

            yield new Question(
                Expression::new(sprintf('%u %s %u', $a, ArithmeticOperator::Addition->value, $b)),
                ComparisonOperator::Equal,
                $answers
            );
        }
    }

    /**
     * Any question requires at least one correct answer, so we have to generate explicitly.
     *
     * @throws \Exception if random_int(...) fails
     */
    private function createCorrectAnswer(int $a, int $b): Expression
    {
        if (random_int(-10, 20) > 0) {
            // create real expression from total so that "a + b = x + y" is true
            $total = $a + $b;
            $x = random_int($this->minOperandValue, $total);
            $y = $total - $x;

            return Expression::new("$x + $y");
        }

        // create an expression simple natural int
        return Expression::new((string) ($a + $b));
    }

    /**
     * TODO: duplicate answer options might get generated.. but for now, it's fine..
     *
     * @param positive-int $count
     *
     * @return list<Expression>
     *
     * @throws \Exception if random_int(...) fails
     */
    private function createRandomAnswers(int $count): array
    {
        $arr = [];
        while ($count > 0) {
            $arr[] = random_int(-10, 20) > 0 // just a simple way to randomize options
                ? Expression::random($this->maxOperandValue) // expr with two operands (preferred option)
                : Expression::new((string) (random_int(0, $this->maxOperandValue) * 2)); // expr with single real number
            --$count;
        }

        return $arr;
    }
}
