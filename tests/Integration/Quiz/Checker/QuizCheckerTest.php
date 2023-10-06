<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Checker;

use App\Math\Domain\Expression\Expression;
use App\Math\Domain\Operators\ComparisonOperator;
use App\Quiz\Checker\CheckQuiz;
use App\Quiz\Domain\CheckedQuiz\Quiz;
use App\Quiz\Domain\SolvedQuiz\AnsweredQuestion;
use App\Quiz\Domain\SolvedQuiz\AnswerOption;
use App\Tests\Integration\MessageBusAwareTestCase;
use Ramsey\Uuid\UuidFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QuizCheckerTest extends KernelTestCase
{
    use MessageBusAwareTestCase;

    public function testQuizPassed(): void
    {
        $quizResult = $this->executeCheckQuizCommand([
            new AnsweredQuestion(new Expression('10 + 10'), ComparisonOperator::Equal, [
                new AnswerOption(new Expression('0'), isSelected: false),
                new AnswerOption(new Expression('2'), isSelected: false),
                new AnswerOption(new Expression('8'), isSelected: false),
                new AnswerOption(new Expression('20'), isSelected: true),
            ]),
        ]);

        $question = $quizResult->getQuestions()[0];
        $this->assertTrue($question->isAnswerCorrect());
        $this->assertNull($question->getAnswers()[0]->isCorrect());
        $this->assertNull($question->getAnswers()[1]->isCorrect());
        $this->assertNull($question->getAnswers()[2]->isCorrect());
        $this->assertTrue($question->getAnswers()[3]->isCorrect());
        $this->assertTrue($quizResult->isPassed());
    }

    public function testQuizFailed(): void
    {
        $quizResult = $this->executeCheckQuizCommand([
            new AnsweredQuestion(new Expression('10 + 10'), ComparisonOperator::Equal, [
                new AnswerOption(new Expression('0'), isSelected: false),
                new AnswerOption(new Expression('2'), isSelected: true),
                new AnswerOption(new Expression('8'), isSelected: false),
                new AnswerOption(new Expression('20'), isSelected: false),
            ]),
        ]);

        $question = $quizResult->getQuestions()[0];
        $this->assertFalse($question->isAnswerCorrect());
        $this->assertNull($question->getAnswers()[0]->isCorrect());
        $this->assertFalse($question->getAnswers()[1]->isCorrect());
        $this->assertNull($question->getAnswers()[2]->isCorrect());
        $this->assertNull($question->getAnswers()[3]->isCorrect());
        $this->assertFalse($quizResult->isPassed());
    }

    /**
     * @dataProvider invalidQuizProvider
     */
    public function testInvalidQuizException(array $answeredQuestions, string $invalidProperty): void
    {
        try {
            $this->executeCheckQuizCommand($answeredQuestions);
            $this->fail('Command must have failed with an error');
        } catch (ValidationFailedException $e) {
            $violations = $e->getViolations();
            $this->assertCount(1, $violations);
            $this->assertSame($invalidProperty, $violations[0]->getPropertyPath());
        }
    }

    public function invalidQuizProvider(): iterable
    {
        return [
            'empty question list' => [
                [],
                'questions',
            ],
            'empty answers list' => [
                [
                    new AnsweredQuestion(new Expression('10 + 10'), ComparisonOperator::Equal, []),
                ],
                'questions[0].answers',
            ],
        ];
    }

    /**
     * @param non-empty-list<AnsweredQuestion> $answeredQuestions
     */
    private function executeCheckQuizCommand(array $answeredQuestions): Quiz
    {
        $quizId = (new UuidFactory())->uuid7();
        $envelope = $this->getCommandBus()->dispatch(new CheckQuiz($quizId, $answeredQuestions));
        $checkedQuiz = $envelope->last(HandledStamp::class)?->getResult();
        $this->assertInstanceOf(Quiz::class, $checkedQuiz);
        $this->assertCount(count($answeredQuestions), $checkedQuiz->getQuestions());

        return $checkedQuiz;
    }
}
