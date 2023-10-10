<?php

declare(strict_types=1);

use App\Infrastructure\Doctrine\Functions\RANDOM;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrineConfig): void {
    $dql = $doctrineConfig->orm()->entityManager('default')->dql();

    $dql->stringFunction('random', RANDOM::class);
};
