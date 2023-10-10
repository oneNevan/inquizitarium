<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolationInterface;

trait InvalidCommandTestCase
{
    /**
     * @dataProvider invalidCommandProvider
     */
    public function testInvalidCommand(object $command, string ...$invalidProperties): void
    {
        try {
            $this->getCommandBus()->dispatch($command);
            $this->fail(sprintf('Command %s should have failed with ValidationFailedException', $command::class));
        } catch (ValidationFailedException $e) {
            $violations = iterator_to_array($e->getViolations());
            $this->assertNotEmpty($violations);
            $this->assertCount(count($invalidProperties), $violations);
            $violations = array_map(static fn (ConstraintViolationInterface $v) => $v->getPropertyPath(), $violations);
            $this->assertEquals($invalidProperties, $violations);
        }
    }
}
