<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Creator\Application;

use App\Quiz\Creator\Application\CreateQuiz;
use App\Quiz\Creator\Domain\Entity\Quiz;
use App\Tests\Integration\InvalidCommandTestCase;
use App\Tests\Integration\MessageBusAwareTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateQuizCommandTest extends KernelTestCase
{
    use MessageBusAwareTestCase;
    use InvalidCommandTestCase;

    public function testCommandHandled(): void
    {
        $quiz = $this->getCommandBus()->execute(new CreateQuiz());
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
