<?php

declare(strict_types=1);

namespace App\Math\Domain\Value;

interface ValueInterface extends \Stringable
{
    public function getValue(): mixed;
}
