<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\Factory;

use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use App\Quiz\Checker\Domain\ValueObject\CheckedQuestion;
use Ramsey\Uuid\UuidInterface;

final readonly class CheckedQuizFactory
{
    /**
     * @param non-empty-list<CheckedQuestion> $checkedQuestions
     */
    public function create(UuidInterface $quizId, array $checkedQuestions, bool $isPassed): CheckedQuiz
    {
        return new CheckedQuiz($quizId, $checkedQuestions, $isPassed);
    }
}
