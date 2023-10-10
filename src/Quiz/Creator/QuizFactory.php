<?php

declare(strict_types=1);

namespace App\Quiz\Creator;

use App\Quiz\Domain\NewQuiz\Quiz;
use App\Quiz\Domain\NewQuiz\QuizFactoryInterface;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\Domain\QuestionPool\QuestionPoolIsEmptyException;
use Ramsey\Uuid\UuidFactory;

final readonly class QuizFactory implements QuizFactoryInterface
{
    public function __construct(
        private UuidFactory $uuidFactory = new UuidFactory(),
    ) {
    }

    public function create(QuestionPoolInterface $pool, int $questionsLimit = null): Quiz
    {
        $cnt = 0;
        $questions = [];
        foreach ($pool->getQuestions() as $question) {
            if (null !== $questionsLimit && $cnt >= $questionsLimit) {
                break;
            }
            $questions[] = $question;
            ++$cnt;
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
