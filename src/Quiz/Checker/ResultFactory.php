<?php

declare(strict_types=1);

namespace App\Quiz\Checker;

use App\Quiz\Domain\QuizResult\Result;
use App\Quiz\Domain\QuizResult\ResultFactoryInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class ResultFactory implements ResultFactoryInterface
{
    public function create(UuidInterface $quizId, array $checkedQuestions, bool $isPassed): Result
    {
        return new Result($quizId, $checkedQuestions, $isPassed);
    }
}
