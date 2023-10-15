<?php

namespace App\Quiz\ResultsStorage\Infrastructure\Orm;

use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use App\Quiz\ResultsStorage\Domain\Repository\ResultRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Result>
 *
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository implements ResultRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    public function add(CheckedQuiz $quiz): void
    {
        $result = (new Result())
            ->setQuizId($quiz->getQuizId())
            ->setIsPassed($quiz->isPassed());

        foreach ($quiz->getQuestions() as $checkedQuestion) {
            $question = (new Question())
                ->setText($checkedQuestion->getExpression()->__toString().' '.$checkedQuestion->getComparisonOperator()->value)
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

        $this->_em->persist($result);
        $this->_em->flush();
    }

    public function countAll(): int
    {
        return $this->count([]);
    }
}
