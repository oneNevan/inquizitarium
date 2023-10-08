<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Creator;

use App\Quiz\Creator\CreateQuiz;
use App\Quiz\Domain\NewQuiz\Quiz;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\QuestionPool\RandomPool;
use App\Tests\Integration\MessageBusAwareTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QuizCreatorTest extends KernelTestCase
{
    use MessageBusAwareTestCase;

    public function testCreateQuiz(): void
    {
        $envelope = $this->getCommandBus()->dispatch(new CreateQuiz());
        $quiz = $envelope->last(HandledStamp::class)?->getResult();
        $this->assertInstanceOf(Quiz::class, $quiz);
        $this->assertNotEmpty($quiz->getQuestions());
        foreach ($quiz->getQuestions() as $question) {
            $this->assertNotEmpty($question->getAnswerOptions());
        }
    }

    public function testEmptyQuestionPoolException(): void
    {
        self::getContainer()->set(QuestionPoolInterface::class, new RandomPool(0));
        $this->expectException(HandlerFailedException::class);
        $this->expectExceptionMessage('Question pool is empty');
        $this->getCommandBus()->dispatch(new CreateQuiz());
    }
}
