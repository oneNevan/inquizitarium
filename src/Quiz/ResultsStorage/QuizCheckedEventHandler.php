<?php

declare(strict_types=1);

namespace App\Quiz\ResultsStorage;

use App\Quiz\Domain\CheckedQuiz\CheckedQuestion;
use App\Quiz\Domain\QuizCheckedEvent;
use App\Quiz\ResultsStorage\Orm\Answer;
use App\Quiz\ResultsStorage\Orm\Question;
use App\Quiz\ResultsStorage\Orm\Result;
use Doctrine\ORM\EntityManagerInterface;

final readonly class QuizCheckedEventHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(QuizCheckedEvent $event): void
    {
        $checkedQuiz = $event->getQuiz();

        $result = (new Result())
            ->setQuizId($checkedQuiz->getQuizId())
            ->setIsPassed($checkedQuiz->isPassed());

        foreach ($checkedQuiz->getQuestions() as $checkedQuestion) {
            $question = (new Question())
                ->setText($this->formatTest($checkedQuestion))
                ->setIsAnswerAccepted($checkedQuestion->isAnswerCorrect());
            $result->addQuestion($question);

            foreach ($checkedQuestion->getAnswers() as $checkedAnswer) {
                $question->addAnswer(
                    (new Answer())
                        ->setIsCorrect($checkedAnswer->isCorrect())
                        ->setText($checkedAnswer->getExpression()->__toString())
                );
            }
        }

        $this->entityManager->persist($result);
        $this->entityManager->flush();
    }

    private function formatTest(CheckedQuestion $checkedQuestion): string
    {
        return $checkedQuestion->getExpression()->__toString().' '.$checkedQuestion->getComparisonOperator()->value;
    }
}
