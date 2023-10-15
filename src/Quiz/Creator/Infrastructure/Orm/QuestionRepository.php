<?php

namespace App\Quiz\Creator\Infrastructure\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @param positive-int|null $limit
     *
     * @return Question[]
     */
    public function getRandom(int $limit = null): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('RANDOM()')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param positive-int|null $limit
     *
     * @return Question[]
     */
    public function getAll(int $limit = null): array
    {
        return $this->findBy([], limit: $limit);
    }
}
