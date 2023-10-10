<?php

declare(strict_types=1);

use App\Infrastructure\Config\DoctrineMappingConfigurator;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\QuestionPool\DatabaseFallbackPool;
use App\Quiz\QuestionPool\DatabasePool;
use App\Quiz\QuestionPool\Orm;
use App\Quiz\QuestionPool\RandomPool;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator, DoctrineConfig $doctrineConfig): void {
    DoctrineMappingConfigurator::configure($doctrineConfig, Orm\Question::class);

    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(Orm\QuestionFixture::class);
    $services->set(Orm\QuestionRepository::class);

    $services->alias(QuestionPoolInterface::class, DatabasePool::class);
    $services->set(RandomPool::class);
    $services->set(DatabasePool::class);
    $services->set(DatabaseFallbackPool::class)
        ->decorate(DatabasePool::class)
        ->args([
            service('.inner'),
            service(RandomPool::class),
        ]);
};
