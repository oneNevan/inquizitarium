<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Checker\Application;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use App\Quiz\Checker\Application\CheckQuiz;
use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use App\Quiz\Checker\Domain\ValueObject\AnsweredQuestion;
use App\Quiz\Checker\Domain\ValueObject\AnswerOption;
use App\Tests\Integration\InvalidCommandTestCase;
use App\Tests\Integration\MessageBusAwareTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CheckQuizCommandTest extends KernelTestCase
{
    use MessageBusAwareTestCase;
    use InvalidCommandTestCase;

    public function testQuizPassed(): void
    {
        $quizResult = $this->executeCheckQuizCommand([
            new AnsweredQuestion(Expression::new('10 + 10'), ComparisonOperator::Equal, [
                new AnswerOption(Expression::new('0'), isSelected: false),
                new AnswerOption(Expression::new('2'), isSelected: false),
                new AnswerOption(Expression::new('8'), isSelected: false),
                new AnswerOption(Expression::new('20'), isSelected: true),
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
            new AnsweredQuestion(Expression::new('10 + 10'), ComparisonOperator::Equal, [
                new AnswerOption(Expression::new('0'), isSelected: false),
                new AnswerOption(Expression::new('2'), isSelected: true),
                new AnswerOption(Expression::new('8'), isSelected: false),
                new AnswerOption(Expression::new('20'), isSelected: false),
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

    public function invalidCommandProvider(): iterable
    {
        return [
            'empty question list' => [
                $this->createCommand([]),
                'questions',
            ],
            'empty answers list' => [
                $this->createCommand([
                    new AnsweredQuestion(Expression::new('10 + 10'), ComparisonOperator::Equal, []),
                ]),
                'questions[0].answers',
            ],
        ];
    }

    private function createCommand(array $answeredQuestions): CheckQuiz
    {
        return new CheckQuiz(
            Uuid::uuid7(),
            $answeredQuestions
        );
    }

    /**
     * @param AnsweredQuestion $answeredQuestions
     */
    private function executeCheckQuizCommand(array $answeredQuestions): CheckedQuiz
    {
        $command = $this->createCommand($answeredQuestions);
        $checkedQuiz = $this->getCommandBus()->execute($command);
        $this->assertInstanceOf(CheckedQuiz::class, $checkedQuiz);
        $this->assertCount(count($answeredQuestions), $checkedQuiz->getQuestions());

        return $checkedQuiz;
    }
}
