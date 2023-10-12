<?php

declare(strict_types=1);

namespace App\Quiz\Domain\SolvedQuiz;

use App\Math\Domain\Expression\ExpressionInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;

final readonly class AnswerOption
{
    /**
     * @psalm-api
     */
    public function __construct(
        private ExpressionInterface $expression,
        // Symfony Serializer cannot deserialize "selected" JSON attribute into $isSelected constructor argument.
        // Though, it works properly in serialization ($isSelected -> "selected"), looks like a bug in object normalizer.
        // So, for now I had to specify serialized name explicitly in my domain object to work around the issue...
        #[SerializedName('selected')]
        private bool $isSelected,
    ) {
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }
}
