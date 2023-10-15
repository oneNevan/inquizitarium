<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Core\Application\CommandBusInterface;
use App\Core\Application\EventBusInterface;

trait MessageBusAwareTestCase
{
    protected function getCommandBus(): CommandBusInterface
    {
        return self::getContainer()->get(CommandBusInterface::class);
    }

    protected function getEventBus(): EventBusInterface
    {
        return self::getContainer()->get(EventBusInterface::class);
    }
}
