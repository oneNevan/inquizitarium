<?php

declare(strict_types=1);

use App\Infrastructure\Config\DoctrineMappingConfigurator;
use App\Quiz\Domain\QuestionPool\QuestionPoolInterface;
use App\Quiz\QuestionPool\DatabasePool;
use App\Quiz\QuestionPool\FallbackPool;
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
    // Using a fallback pool for the database pool, so that, by default, users don't have to run fixtures
    // or create entries in the database - it should just work out-of-the-box thanks to RandomPool as the fallback
    $services->set(FallbackPool::class)
        ->autowire(false)
        ->decorate(DatabasePool::class)
        ->args([
            '$decoratedPool' => service('.inner'),
            '$fallbackPool' => service(RandomPool::class),
        ]);
};
