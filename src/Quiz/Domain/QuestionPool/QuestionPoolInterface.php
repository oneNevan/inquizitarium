<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuestionPool;

interface QuestionPoolInterface
{
    /**
     * @param positive-int|null $limit
     *
     * @return iterable<Question>
     */
    public function getQuestions(int $limit = null): iterable;
}
