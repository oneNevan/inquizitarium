<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Component\Messenger\MessageBusInterface;

trait MessageBusAwareTestCase
{
    protected function getCommandBus(): MessageBusInterface
    {
        return self::getContainer()->get('command.bus');
    }

    protected function getEventBus(): MessageBusInterface
    {
        return self::getContainer()->get('event.bus');
    }
}
