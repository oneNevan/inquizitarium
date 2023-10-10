<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Creator;

use App\Quiz\Creator\CreateQuiz;
use App\Quiz\Domain\NewQuiz\Quiz;
use App\Tests\Integration\InvalidCommandTestCase;
use App\Tests\Integration\MessageBusAwareTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QuizCreatorTest extends KernelTestCase
{
    use MessageBusAwareTestCase;
    use InvalidCommandTestCase;

    public function testCommandHandled(): void
    {
        $envelope = $this->getCommandBus()->dispatch(new CreateQuiz());
        $quiz = $envelope->last(HandledStamp::class)?->getResult();
        $this->assertInstanceOf(Quiz::class, $quiz);
    }

    public function invalidCommandProvider(): iterable
    {
        return [
            'zero limit' => [new CreateQuiz(0), 'questionsCount'],
            'negative limit' => [new CreateQuiz(-1), 'questionsCount'],
        ];
    }
}
