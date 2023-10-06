<?php

declare(strict_types=1);

namespace App\Quiz\Domain\QuizResult;

use Ramsey\Uuid\UuidInterface;

interface ResultFactoryInterface
{
    /**
     * @param non-empty-list<CheckedQuestion> $checkedQuestions
     */
    public function create(UuidInterface $quizId, array $checkedQuestions, bool $isPassed): Result;
}
