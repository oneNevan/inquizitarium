<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuestionPool;

interface QuestionPoolInterface
{
    /**
     * @return iterable<Question>
     */
    public function getQuestions(): iterable;
}
