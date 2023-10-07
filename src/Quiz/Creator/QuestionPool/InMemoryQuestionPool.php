<?php

declare(strict_types=1);

namespace App\Quiz\Creator\QuestionPool;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Expression\RandomExpression;
use App\Math\Domain\Operators\ArithmeticOperator;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Quiz\Domain\QuestionPool\Question;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

/**
 * Dummy pool for testing purposes.
 */
final readonly class InMemoryQuestionPool implements QuestionPoolInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private int $questionsCount = 10,
        private int $answersPerQuestionCount = 5,
    ) {
    }

    public function getQuestions(): iterable
    {
        return new \ArrayIterator(iterator_to_array($this->getIterator()));
    }

    /**
     * @return \Generator<Question>
     */
    private function getIterator(): \Generator
    {
        $i = $this->questionsCount;
        while ($i > 0) {
            $question = sprintf('%u %s %u', $i, ArithmeticOperator::Addition->value, $i);
            $answers = [
                new Expression((string) ($i + $i)), // indeed correct answer (at least one required)
            ];
            for ($j = 1; $j < $this->answersPerQuestionCount; ++$j) {
                $answers[] = new RandomExpression($this->questionsCount);
            }
            shuffle($answers); // so that correct answer is not always first

            yield new Question(new Expression($question), ComparisonOperator::Equal, $answers);
            --$i;
        }
    }
}
