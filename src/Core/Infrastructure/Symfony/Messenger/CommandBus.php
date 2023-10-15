<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\Messenger;

use App\Core\Application\CommandBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class CommandBus implements CommandBusInterface
{
    public const BUS_NAME = 'command.bus';

    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function execute(object $command, string $resultType = null): mixed
    {
        $result = $this->commandBus->dispatch($command)->last(HandledStamp::class)?->getResult();

        if (null === $resultType) {
            return $result;
        }

        if (!$result instanceof $resultType) {
            $message = sprintf(
                'Handler for command %s did not return an object of expected type %s, %s returned',
                $command::class,
                $resultType,
                get_debug_type($result),
            );
            throw new \LogicException($message);
        }

        return $result;
    }
}
