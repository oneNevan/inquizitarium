<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Creator\Application;

use App\Core\Application\EventBusInterface;
use App\Quiz\Creator\Application\CreateQuiz;
use App\Quiz\Creator\Application\CreateQuizHandler;
use App\Quiz\Creator\Domain\Factory\QuestionPoolIsEmptyException;
use App\Quiz\Creator\Domain\Factory\QuizFactory;
use App\Quiz\Creator\Domain\QuizCreatedEvent;
use App\Quiz\Creator\Domain\Service\QuestionPool\RandomPool;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;
use App\Tests\Integration\MessageBusAwareTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateQuizHandlerTest extends KernelTestCase
{
    use MessageBusAwareTestCase;

    public function testQuizCreated(): void
    {
        $handler = $this->createHandler(new RandomPool(3));
        $quiz = $handler(new CreateQuiz());

        $this->assertCount(3, $quiz->getQuestions());
        foreach ($quiz->getQuestions() as $question) {
            $this->assertNotEmpty($question->getAnswerOptions());
        }
    }

    public function testEmptyQuestionPoolException(): void
    {
        $eventBus = $this->createMock(EventBusInterface::class);
        $eventBus->expects($this->never())->method('dispatch');
        $handler = new CreateQuizHandler(new RandomPool(0), new QuizFactory(), $eventBus);
        $this->expectExceptionObject(new QuestionPoolIsEmptyException());
        $handler(new CreateQuiz());
    }

    /**
     * @dataProvider questionsLimitProvider
     */
    public function testQuestionsLimit(CreateQuiz $command, int $expectedCount, int $poolSize = 20): void
    {
        $handler = $this->createHandler(new RandomPool($poolSize));
        $quiz = $handler($command);
        $this->assertCount($expectedCount, $quiz->getQuestions());
    }

    public function questionsLimitProvider(): iterable
    {
        return [
            'default' => [new CreateQuiz(), 10],
            'custom' => [new CreateQuiz(3), 3],
            'limit exceeds question pool size' => [new CreateQuiz(50), 20],
            'limit is undefined' => [new CreateQuiz(null), 20],
        ];
    }

    private function createHandler(QuestionPoolInterface $pool): CreateQuizHandler
    {
        return new CreateQuizHandler($pool, new QuizFactory(), $this->createEventBus());
    }

    private function createEventBus(): EventBusInterface&MockObject
    {
        $eventBus = $this->createMock(EventBusInterface::class);
        $eventBus->expects($this->once())->method('dispatch')
            ->willReturnCallback(function ($message) {
                $this->assertInstanceOf(QuizCreatedEvent::class, $message);
            });

        return $eventBus;
    }
}
