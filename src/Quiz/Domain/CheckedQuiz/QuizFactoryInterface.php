<?php

declare(strict_types=1);

namespace App\Quiz\Domain\CheckedQuiz;

use Ramsey\Uuid\UuidInterface;

interface QuizFactoryInterface
{
    /**
     * @param non-empty-list<CheckedQuestion> $checkedQuestions
     */
    public function create(UuidInterface $quizId, array $checkedQuestions, bool $isPassed): Quiz;
}
