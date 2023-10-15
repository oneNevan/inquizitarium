<?php

declare(strict_types=1);

namespace App\Quiz\Checker\Domain\ValueObject;

use App\Math\Domain\ValueObject\Expression;
use Symfony\Component\Serializer\Annotation\SerializedName;

final readonly class AnswerOption
{
    public function __construct(
        private Expression $expression,
        // Symfony Serializer cannot deserialize "selected" JSON attribute into $isSelected constructor argument.
        // Though, it works properly in serialization ($isSelected -> "selected"), looks like a bug in object normalizer.
        // So, for now I had to specify serialized name explicitly in my domain object to work around the issue...
        #[SerializedName('selected')]
        private bool $isSelected,
    ) {
    }

    public function getExpression(): Expression
    {
        return $this->expression;
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }
}
