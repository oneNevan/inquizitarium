<?php

declare(strict_types=1);

namespace App\Quiz\Checker;

use App\Quiz\Domain\CheckedQuiz\Quiz;
use App\Quiz\Domain\CheckedQuiz\QuizFactoryInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class CheckedQuizFactory implements QuizFactoryInterface
{
    public function create(UuidInterface $quizId, array $checkedQuestions, bool $isPassed): Quiz
    {
        return new Quiz($quizId, $checkedQuestions, $isPassed);
    }
}
