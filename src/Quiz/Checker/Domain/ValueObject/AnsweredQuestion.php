<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\ValueObject;

use App\Math\Domain\ValueObject\Expression;
use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AnsweredQuestion
{
    /**
     * @param non-empty-list<AnswerOption> $answers
     */
    public function __construct(
        private Expression $expression,
        private ComparisonOperator $comparisonOperator,
        #[Assert\NotBlank]
        private array $answers,
    ) {
    }

    public function getExpression(): Expression
    {
        return $this->expression;
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
