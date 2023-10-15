<?php

namespace App\Quiz\Creator\Infrastructure\Orm;

use App\Math\Domain\ValueObject\Operator\ComparisonOperator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuestionFixture extends Fixture
{
    /**
     * Just a sample static pool of questions.
     */
    private const POOL = [
        '1 + 1' => ['3', '2', '0'],
        '2 + 2' => ['4', '3 + 1', '10'],
        '3 + 3' => ['1 + 5', '1', '6', '2 + 4'],
        '4 + 4' => ['8', '4', '0', '0 + 8'],
        '5 + 5' => ['6', '18', '10', '9', '0'],
        '6 + 6' => ['3', '9', '0', '12', '5 + 7'],
        '7 + 7' => ['5', '14'],
        '8 + 8' => ['16', '12', '9', '5'],
        '9 + 9' => ['18', '9', '17 + 1', '2 + 16'],
        '10 + 10' => ['0', '2', '8', '20'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::POOL as $question => $answers) {
            $manager->persist((new Question())
                ->setExpression($question)
                ->setComparison(ComparisonOperator::Equal)
                ->setAnswerOptions($answers));
        }

        $manager->flush();
    }
}
