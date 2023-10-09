<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAwareTestCase
{
    protected static function getEntityManager(string $name = 'default'): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager($name);
    }
}
