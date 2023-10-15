<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Checker\Domain\Service;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use App\Quiz\Checker\Domain\Service\FuzzyQuestionChecking;
use App\Quiz\Checker\Domain\Service\QuestionHasNoSelectedAnswersException;
use App\Quiz\Checker\Domain\ValueObject\AnsweredQuestion;
use App\Quiz\Checker\Domain\ValueObject\AnswerOption;
use PHPUnit\Framework\TestCase;

class FuzzyQuestionCheckingTest extends TestCase
{
    private const QUESTION = '9 + 9';
    private const OPTIONS = ['#1' => '18', '#2' => '9', '#3' => '17 + 1', '#4' => '2 + 16'];

    /**
     * @dataProvider answeredQuestionsProvider
     */
    public function testFuzzyCheckingResults(AnsweredQuestion $answeredQuestion, bool $expectedResult): void
    {
        $checkedQuestion = (new FuzzyQuestionChecking())->check($answeredQuestion);

        $this->assertSame($expectedResult, $checkedQuestion->isAnswerCorrect());
    }

    /**
     * Creates data set to test question '9 + 9' = ?.
     * Possible answers:
     *  - #1: '18'
     *  - #2: '9' (the only wrong answer)
     *  - #3: '17 + 1'
     *  - #4: '2 + 16'.
     *
     * Valid answers are: 1 OR 3 OR 4 OR (1 AND 3) OR (1 AND 4) OR (3 AND 4) OR (1 AND 3 AND 4)
     */
    public function answeredQuestionsProvider(): iterable
    {
        $questionExpr = Expression::new(self::QUESTION);

        $answers = [
            'Selected: #1' => [array_combine(self::OPTIONS, [true, false, false, false]), true],
            'Selected: #3' => [array_combine(self::OPTIONS, [false, false, true, false]), true],
            'Selected: #4' => [array_combine(self::OPTIONS, [false, false, false, true]), true],
            'Selected: #1 and #3' => [array_combine(self::OPTIONS, [true, false, true, false]), true],
            'Selected: #1 and #4' => [array_combine(self::OPTIONS, [true, false, false, true]), true],
            'Selected: #3 and #4' => [array_combine(self::OPTIONS, [false, false, true, true]), true],
            'Selected: #1 and #3 and #4' => [array_combine(self::OPTIONS, [true, false, true, true]), true],
        ];

        foreach ($answers as $title => [$options]) {
            $options['9'] = true; // mark '9' (#2) as selected which is indeed wrong answer
            $answers["$title and #2"] = [$options, false];
        }
        $answers['Selected: #2'] = [$options, false];

        foreach ($answers as $title => [$options, $isCorrect]) {
            foreach ($options as $expr => $isSelected) {
                $options[$expr] = new AnswerOption(Expression::new((string) $expr), $isSelected);
            }
            yield $title => [
                new AnsweredQuestion($questionExpr, ComparisonOperator::Equal, array_values($options)),
                $isCorrect,
            ];
        }
    }

    public function testNothingIsSelected(): void
    {
        $questionExpr = Expression::new(self::QUESTION);
        $question = new AnsweredQuestion($questionExpr, ComparisonOperator::Equal, [
            new AnswerOption(Expression::new(self::OPTIONS['#1']), false),
            new AnswerOption(Expression::new(self::OPTIONS['#2']), false),
            new AnswerOption(Expression::new(self::OPTIONS['#3']), false),
            new AnswerOption(Expression::new(self::OPTIONS['#4']), false),
        ]);

        $this->expectExceptionObject(new QuestionHasNoSelectedAnswersException($questionExpr, ComparisonOperator::Equal));
        (new FuzzyQuestionChecking())->check($question);
    }
}
