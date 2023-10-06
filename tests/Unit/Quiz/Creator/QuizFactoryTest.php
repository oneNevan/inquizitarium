<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Creator;

use App\Quiz\Creator\QuestionPool\InMemoryQuestionPool;
use App\Quiz\Creator\QuizFactory;
use App\Quiz\Domain\QuestionPool\QuestionPoolIsEmptyException;
use PHPUnit\Framework\TestCase;

class QuizFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new QuizFactory();
        $quiz = $factory->create(new InMemoryQuestionPool(questionsCount: 5, answersPerQuestionCount: 3));
        $this->assertCount(5, $quiz->getQuestions());

        foreach ($quiz->getQuestions() as $question) {
            $this->assertCount(3, $question->getAnswerOptions());
        }
    }

    public function testEmptyQuestionPool(): void
    {
        $this->expectExceptionObject(new QuestionPoolIsEmptyException());
        (new QuizFactory())->create(new InMemoryQuestionPool(questionsCount: 0));
    }
}
