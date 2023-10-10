<?php

declare(strict_types=1);

namespace App\Quiz\QuestionPool;

use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;

/**
 * Decorator for empty database pool.
 *
 * Using a fallback pool if the database pool is empty, so that by default users don't have to run fixtures
 * or fill the database - it should just work out for the box :)
 */
final readonly class DatabaseFallbackPool implements QuestionPoolInterface
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
