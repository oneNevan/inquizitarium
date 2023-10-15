<?php

declare(strict_types=1);

use App\Core\Infrastructure\Doctrine\Config\DoctrineMappingConfigurator;
use App\Core\Infrastructure\Symfony\Messenger\EventBus;
use App\Quiz\ResultsStorage\Application\QuizCheckedEventHandler;
use App\Quiz\ResultsStorage\Domain\Repository\ResultRepositoryInterface;
use App\Quiz\ResultsStorage\Infrastructure\Orm\AnswerRepository;
use App\Quiz\ResultsStorage\Infrastructure\Orm\QuestionRepository;
use App\Quiz\ResultsStorage\Infrastructure\Orm\Result;
use App\Quiz\ResultsStorage\Infrastructure\Orm\ResultRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (
    ContainerConfigurator $configurator,
    DoctrineConfig $doctrineConfig,
    ContainerBuilder $builder,
): void {
    DoctrineMappingConfigurator::configure($doctrineConfig, entity: Result::class);

    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(QuizCheckedEventHandler::class)->tag('messenger.message_handler', [
        'bus' => EventBus::BUS_NAME,
    ]);

    $services->alias(ResultRepositoryInterface::class, ResultRepository::class);

    $services->set(ResultRepository::class);
    $services->set(QuestionRepository::class);
    $services->set(AnswerRepository::class);

    if ('test' === $configurator->env()) {
        $builder->getAlias(ResultRepositoryInterface::class)->setPublic(true);
    }
};
