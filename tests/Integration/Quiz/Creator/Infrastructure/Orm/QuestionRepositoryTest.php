<?php

declare(strict_types=1);

namespace App\Tests\Integration\Quiz\Creator\Infrastructure\Orm;

use App\Quiz\Creator\Infrastructure\Orm\Question;
use App\Tests\Integration\EntityManagerAwareTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class QuestionRepositoryTest extends KernelTestCase
{
    use EntityManagerAwareTestCase;

    public function testGetRandom(): void
    {
        $repository = self::getEntityManager()->getRepository(Question::class);

        $list1 = $repository->getRandom(limit: 5);
        $list2 = $repository->getRandom(limit: 5);

        $this->assertCount(5, $list1);
        $this->assertCount(5, $list2);

        $this->assertNotEquals($list1, $list2);
    }

    public function testGetAll(): void
    {
        $repository = self::getEntityManager()->getRepository(Question::class);

        $list1 = $repository->getAll(limit: 5);
        $list2 = $repository->getAll(limit: 5);

        $this->assertCount(5, $list1);
        $this->assertCount(5, $list2);

        $this->assertEquals($list1, $list2);
    }
}
