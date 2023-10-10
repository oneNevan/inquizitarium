<?php

declare(strict_types=1);

namespace App\Quiz\QuestionPool;

use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

/**
 * Decorator for question pool, using another pool as fallback, if the decorated one is empty.
 */
final readonly class FallbackPool implements QuestionPoolInterface
{
    public function __construct(
        private QuestionPoolInterface $decoratedPool,
        private QuestionPoolInterface $fallbackPool = new RandomPool(),
    ) {
    }

    /**
     * @throws \Exception if RandomPool fails
     */
    public function getQuestions(): iterable
    {
        $cnt = 0;
        foreach ($this->decoratedPool->getQuestions() as $question) {
            yield $question;
            ++$cnt;
        }

        if (0 === $cnt) {
            yield from $this->fallbackPool->getQuestions();
        }
    }
}