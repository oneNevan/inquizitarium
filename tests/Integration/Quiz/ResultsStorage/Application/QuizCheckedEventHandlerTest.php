<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\ResultsStorage\Application;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use App\Quiz\Checker\Domain\Entity\CheckedQuiz;
use App\Quiz\Checker\Domain\QuizCheckedEvent;
use App\Quiz\Checker\Domain\ValueObject\CheckedAnswer;
use App\Quiz\Checker\Domain\ValueObject\CheckedQuestion;
use App\Quiz\ResultsStorage\Domain\Repository\ResultRepositoryInterface;
use App\Tests\Integration\MessageBusAwareTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class QuizCheckedEventHandlerTest extends KernelTestCase
{
    use MessageBusAwareTestCase;

    public function testEventHandled(): void
    {
        // arrange
        /** @var ResultRepositoryInterface $repository */
        $repository = self::getContainer()->get(ResultRepositoryInterface::class);
        $countBefore = $repository->countAll();

        // act
        $this->getEventBus()->dispatch(new QuizCheckedEvent(
            new CheckedQuiz(Uuid::uuid7(), [
                new CheckedQuestion(Expression::new('2 + 2'), ComparisonOperator::Equal, [
                    new CheckedAnswer(Expression::new('1 + 3'), isCorrect: true),
                    new CheckedAnswer(Expression::new('4 + 2'), isCorrect: null),
                ], isAnswerCorrect: true),
            ], true),
        ));

        // assert
        $this->assertSame(
            $countBefore + 1,
            $repository->countAll(),
            'Failed asserting that event handled added checked quiz to result repository',
        );
    }
}
