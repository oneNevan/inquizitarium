<?php

namespace App\Quiz\Creator\Infrastructure\Orm;

use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use Doctrine\ORM\Mapping as ORM;

/**
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: 'quiz_question_pool')]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $expression;

    #[ORM\Column(length: 5)]
    private ComparisonOperator $comparison;

    /**
     * @var non-empty-list<string>
     */
    #[ORM\Column]
    private array $answerOptions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function setExpression(string $expression): static
    {
        $this->expression = $expression;

        return $this;
    }

    public function getComparison(): ComparisonOperator
    {
        return $this->comparison;
    }

    public function setComparison(ComparisonOperator $comparison): static
    {
        $this->comparison = $comparison;

        return $this;
    }

    /**
     * @return non-empty-list<string>
     */
    public function getAnswerOptions(): array
    {
        return $this->answerOptions;
    }

    /**
     * @param non-empty-list<string> $answerOptions
     */
    public function setAnswerOptions(array $answerOptions): static
    {
        $this->answerOptions = $answerOptions;

        return $this;
    }
}
