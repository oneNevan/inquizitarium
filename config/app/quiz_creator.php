<?php

declare(strict_types=1);

use App\Core\Infrastructure\Doctrine\Config\DoctrineMappingConfigurator;
use App\Core\Infrastructure\Symfony\Messenger\CommandBus;
use App\Quiz\Creator\Application\CreateQuizHandler;
use App\Quiz\Creator\Domain\Factory\QuizFactory;
use App\Quiz\Creator\Domain\Service\QuestionPool\FallbackPool;
use App\Quiz\Creator\Domain\Service\QuestionPool\RandomPool;
use App\Quiz\Creator\Domain\Service\QuestionPoolInterface;
use App\Quiz\Creator\Infrastructure\ApiController;
use App\Quiz\Creator\Infrastructure\Doctrine\Functions\Random;
use App\Quiz\Creator\Infrastructure\Orm\Question;
use App\Quiz\Creator\Infrastructure\Orm\QuestionFixture;
use App\Quiz\Creator\Infrastructure\Orm\QuestionRepository;
use App\Quiz\Creator\Infrastructure\Service\DatabasePool;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator, DoctrineConfig $doctrineConfig): void {
    // Doctrine ORM mapping
    DoctrineMappingConfigurator::configure($doctrineConfig, Question::class);

    // Doctrine DQL
    $dql = $doctrineConfig->orm()->entityManager('default')->dql();
    $dql->stringFunction('random', Random::class);

    // Services
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CreateQuizHandler::class)->tag('messenger.message_handler', [
        'bus' => CommandBus::BUS_NAME,
    ]);

    $services->set(QuizFactory::class);
    $services->set(QuestionFixture::class);
    $services->set(QuestionRepository::class);

    $services->alias(QuestionPoolInterface::class, DatabasePool::class);
    $services->set(RandomPool::class);
    $services->set(DatabasePool::class)
        ->arg('$shuffle', 'test' !== $configurator->env());
    // Using a fallback pool for the database pool, so that, by default, users don't have to run fixtures
    // or create entries in the database - it should just work out-of-the-box thanks to RandomPool as the fallback
    $services->set(FallbackPool::class)
        ->autowire(false)
        ->decorate(DatabasePool::class)
        ->args([
            '$decoratedPool' => service('.inner'),
            '$fallbackPool' => service(RandomPool::class),
        ]);

    $services->set(ApiController::class)->tag('controller.service_arguments');
};
