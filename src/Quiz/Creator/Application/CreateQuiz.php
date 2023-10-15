<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Application;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateQuiz
{
    /**
     * @param positive-int|null $questionsCount null - no limitations (all questions from question pool)
     */
    public function __construct(
        #[Assert\AtLeastOneOf([
            new Assert\Positive(),
            new Assert\IsNull(),
        ])]
        private ?int $questionsCount = 10,
    ) {
    }

    /**
     * @return positive-int|null
     */
    public function getQuestionsCount(): ?int
    {
        return $this->questionsCount;
    }
}
