<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain\Service;

use App\Quiz\Creator\Domain\ValueObject\Question;

interface QuestionPoolInterface
{
    /**
     * @param positive-int|null $limit
     *
     * @return iterable<Question>
     */
    public function getQuestions(int $limit = null): iterable;
}
