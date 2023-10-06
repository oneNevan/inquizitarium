<?php

declare(strict_types=1);

namespace App\Quiz\Domain\SolvedQuiz;

use App\Math\Domain\Expression\ExpressionInterface;
use App\Math\Domain\Operators\ComparisonOperator;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AnsweredQuestion
{
    /**
     * @psalm-api
     *
     * @param non-empty-list<AnswerOption> $answers
     */
    public function __construct(
        private ExpressionInterface $question,
        private ComparisonOperator $comparisonOperator,
        #[Assert\NotBlank]
        private array $answers,
    ) {
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->question;
    }

    public function getComparisonOperator(): ComparisonOperator
    {
        return $this->comparisonOperator;
    }

    /**
     * @return non-empty-list<AnswerOption>
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }
}
