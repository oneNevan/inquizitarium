<?php

declare(strict_types=1);

namespace App\Quiz\ResultsStorage;

use App\Math\Domain\Expression\ExpressionInterface;
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
            $text = $this->formatExpr($checkedQuestion->getExpression())
                .$checkedQuestion->getComparisonOperator()->value;

            $question = (new Question())
                ->setText($text)
                ->setIsAnswerAccepted($checkedQuestion->isAnswerCorrect());
            $result->addQuestion($question);

            foreach ($checkedQuestion->getAnswers() as $checkedAnswer) {
                $question->addAnswer(
                    (new Answer())
                        ->setIsCorrect($checkedAnswer->isCorrect())
                        ->setText($this->formatExpr($checkedAnswer->getExpression()))
                );
            }
        }

        $this->entityManager->persist($result);
        $this->entityManager->flush();
    }

    /**
     * Removes all spaces from expressions before saving it in database.
     */
    private function formatExpr(ExpressionInterface $expr): string
    {
        return preg_replace(pattern: '/\s+/', replacement: '', subject: (string) $expr);
    }
}
