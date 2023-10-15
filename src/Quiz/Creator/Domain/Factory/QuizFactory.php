<?php

declare(strict_types=1);

namespace App\Quiz\Creator\Domain\Factory;

use App\Quiz\Creator\Domain\Entity\Quiz;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;
use Ramsey\Uuid\UuidFactory;

final readonly class QuizFactory
{
    public function __construct(
        private UuidFactory $uuidFactory = new UuidFactory(),
    ) {
    }

    /**
     * @param positive-int|null $questionsLimit
     */
    public function create(QuestionPoolInterface $pool, int $questionsLimit = null): Quiz
    {
        $questions = [];
        foreach ($pool->getQuestions($questionsLimit) as $question) {
            $questions[] = $question;
        }

        if (empty($questions)) {
            throw new QuestionPoolIsEmptyException();
        }

        return new Quiz(
            $this->uuidFactory->uuid7(),
            $questions,
        );
    }
}
