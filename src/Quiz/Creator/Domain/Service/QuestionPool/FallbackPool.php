<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain\Service\QuestionPool;

use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;

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
    public function getQuestions(int $limit = null): iterable
    {
        $cnt = 0;
        foreach ($this->decoratedPool->getQuestions($limit) as $question) {
            yield $question;
            ++$cnt;
        }

        if (0 === $cnt) {
            yield from $this->fallbackPool->getQuestions($limit);
        }
    }
}
