<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Creator;

use App\Quiz\Creator\QuizFactory;
use App\Quiz\Domain\QuestionPool\QuestionPoolIsEmptyException;
use App\Quiz\QuestionPool\RandomPool;
use PHPUnit\Framework\TestCase;

class QuizFactoryTest extends TestCase
{
    public function testCreateWithRandomPool(): void
    {
        $factory = new QuizFactory();
        $quiz = $factory->create(new RandomPool(poolSize: 10, maxAnswerOptions: 5));
        $this->assertCount(10, $quiz->getQuestions());

        foreach ($quiz->getQuestions() as $question) {
            $count = count($question->getAnswerOptions());
            $this->assertGreaterThan(1, $count);
            $this->assertLessThanOrEqual(5, $count);
        }
    }

    public function testEmptyQuestionPool(): void
    {
        $this->expectExceptionObject(new QuestionPoolIsEmptyException());
        (new QuizFactory())->create(new RandomPool(poolSize: 0));
    }
}
