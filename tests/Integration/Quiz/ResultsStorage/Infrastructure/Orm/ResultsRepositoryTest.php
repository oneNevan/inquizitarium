<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\ResultsStorage\Infrastructure\Orm;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use App\Quiz\Checker\Domain\ValueObject\CheckedAnswer;
use App\Quiz\Checker\Domain\ValueObject\CheckedQuestion;
use App\Quiz\ResultsStorage\Infrastructure\Orm\Answer;
use App\Quiz\ResultsStorage\Infrastructure\Orm\Result;
use App\Quiz\ResultsStorage\Infrastructure\Orm\ResultRepository;
use App\Tests\Integration\EntityManagerAwareTestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResultsRepositoryTest extends KernelTestCase
{
    use EntityManagerAwareTestCase;

    public function testResultSavedInDatabase(): void
    {
        /** @var ResultRepository $repository */
        $repository = $this->getRepository();
        $quiz = $this->createSampleQuiz();

        $repository->add($quiz);
        $result = $repository->findOneBy(['quizId' => $quiz->getQuizId()]);

        // result
        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotNull($result->getId());
        $this->assertSame($quiz->getQuizId(), $result->getQuizId());
        /** @var Answer $questions */
        $questions = $result->getQuestions();
        $this->assertCount(2, $questions);

        // question 1
        $this->assertNotNull($questions[0]->getId());
        $this->assertTrue($questions[0]->isAnswerAccepted());
        $this->assertSame('2 + 2 =', $questions[0]->getText());
        /** @var Answer $answers */
        $answers = $questions[0]->getAnswers();
        $this->assertCount(2, $answers);
        $this->assertSame('1 + 3', $answers[0]->getText());
        $this->assertNotNull($answers[0]->getId());
        $this->assertTrue($answers[0]->isCorrect());
        $this->assertSame('4 + 2', $answers[1]->getText());
        $this->assertNotNull($answers[1]->getId());
        $this->assertNull($answers[1]->isCorrect());

        // question 2
        $this->assertNotNull($questions[1]->getId());
        $this->assertFalse($questions[1]->isAnswerAccepted());
        $this->assertSame('3 + 7 =', $questions[1]->getText());
        /** @var Answer $answers */
        $answers = $questions[1]->getAnswers();
        $this->assertCount(3, $answers);
        $this->assertSame('6 + 2', $answers[0]->getText());
        $this->assertNotNull($answers[0]->getId());
        $this->assertFalse($answers[0]->isCorrect());
        $this->assertSame('2 + 3', $answers[1]->getText());
        $this->assertNotNull($answers[1]->getId());
        $this->assertNull($answers[1]->isCorrect());
        $this->assertSame('10', $answers[2]->getText());
        $this->assertNotNull($answers[2]->getId());
        $this->assertTrue($answers[2]->isCorrect());
    }

    public function testDuplicateException(): void
    {
        $uuid = Uuid::uuid7();
        $quiz = $this->createSampleQuiz($uuid);
        $this->getRepository()->add($quiz);

        $this->expectExceptionMessage('duplicate key value violates unique constraint');
        $this->getRepository()->add($quiz);
    }

    private function createSampleQuiz(UuidInterface $uuid = null): CheckedQuiz
    {
        $uuid ??= Uuid::uuid7();

        return new CheckedQuiz($uuid, [
            new CheckedQuestion(Expression::new('2 + 2'), ComparisonOperator::Equal, [
                new CheckedAnswer(Expression::new('1 + 3'), isCorrect: true),
                new CheckedAnswer(Expression::new('4 + 2'), isCorrect: null),
            ], isAnswerCorrect: true),
            new CheckedQuestion(Expression::new('3 + 7'), ComparisonOperator::Equal, [
                new CheckedAnswer(Expression::new('6 + 2'), isCorrect: false),
                new CheckedAnswer(Expression::new('2 + 3'), isCorrect: null),
                new CheckedAnswer(Expression::new('10'), isCorrect: true),
            ], isAnswerCorrect: false),
        ], isPassed: false);
    }

    private function getRepository(): ResultRepository
    {
        return self::getEntityManager()->getRepository(Result::class);
    }
}
