<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\Messenger;

use App\Core\Application\EventBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class EventBus implements EventBusInterface
{
    public const BUS_NAME = 'event.bus';

    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    public function dispatch(object $event): void
    {
        $this->eventBus->dispatch($event, [
            new DispatchAfterCurrentBusStamp(),
        ]);
    }
}
