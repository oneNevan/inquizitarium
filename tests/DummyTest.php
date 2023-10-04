<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase
{
    public function testDummyMethod(): void
    {
        // emulating failure to test CI
        $this->assertSame('42', 42);
    }
}
