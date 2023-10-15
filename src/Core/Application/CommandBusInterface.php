<?php

declare(strict_types=1);

namespace App\Core\Application;

interface CommandBusInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T>|null $resultType Defines a type of object that should be
     *
     * @psalm-return ($resultType is null ? null : T)
     */
    public function execute(object $command, string $resultType = null): mixed;
}
