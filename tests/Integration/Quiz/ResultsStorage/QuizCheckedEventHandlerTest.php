<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\ResultsStorage;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Quiz\Domain\CheckedQuiz\CheckedAnswer;
use App\Quiz\Domain\CheckedQuiz\CheckedQuestion;
use App\Quiz\Domain\CheckedQuiz\Quiz;
use App\Quiz\Domain\QuizCheckedEvent;
use App\Quiz\ResultsStorage\Orm\Answer;
use App\Quiz\ResultsStorage\Orm\Question;
use App\Quiz\ResultsStorage\Orm\Result;
use App\Tests\Integration\EntityManagerAwareTestCase;
use App\Tests\Integration\MessageBusAwareTestCase;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class QuizCheckedEventHandlerTest extends KernelTestCase
{
    use MessageBusAwareTestCase;
    use EntityManagerAwareTestCase;

    public function testResultSavedInDatabase(): void
    {
        $quiz = $this->createSampleQuiz();
        $this->getEventBus()->dispatch(new QuizCheckedEvent($quiz));

        $repository = self::getEntityManager()->getRepository(Result::class);
        $result = $repository->findOneBy(['quizId' => $quiz->getQuizId()]);

        // result
        $this->assertInstanceOf(Result::class, $result);
        $this->assertNotNull($result->getId());
        $this->assertSame($quiz->getQuizId(), $result->getQuizId());
        /** @var list<Question> $questions */
        $questions = $result->getQuestions();
        $this->assertCount(2, $questions);

        // question 1
        $this->assertNotNull($questions[0]->getId());
        $this->assertTrue($questions[0]->isAnswerAccepted());
        $this->assertSame('2 + 2 =', $questions[0]->getText());
        /** @var list<Answer> $answers */
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
        /** @var list<Answer> $answers */
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
        $uuid = (new UuidFactory())->uuid7();
        $this->getEventBus()->dispatch(new QuizCheckedEvent($this->createSampleQuiz($uuid)));

        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('duplicate key value violates unique constraint');
        $this->getEventBus()->dispatch(new QuizCheckedEvent($this->createSampleQuiz($uuid)));
    }

    private function createSampleQuiz(UuidInterface $uuid = null): Quiz
    {
        $uuid ??= (new UuidFactory())->uuid7();

        return new Quiz($uuid, [
            new CheckedQuestion(new Expression('2 + 2'), ComparisonOperator::Equal, [
                new CheckedAnswer(new Expression('1 + 3'), isCorrect: true),
                new CheckedAnswer(new Expression('4 + 2'), isCorrect: null),
            ], isAnswerCorrect: true),
            new CheckedQuestion(new Expression('3 + 7'), ComparisonOperator::Equal, [
                new CheckedAnswer(new Expression('6 + 2'), isCorrect: false),
                new CheckedAnswer(new Expression('2 + 3'), isCorrect: null),
                new CheckedAnswer(new Expression('10'), isCorrect: true),
            ], isAnswerCorrect: false),
        ], isPassed: false);
    }
}
