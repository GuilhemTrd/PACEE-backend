<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class SetupTest extends TestCase
{
    public function testAddition(): void
    {
        $this->assertEquals(4, 2 + 2, "2 + 2 devrait être égal à 4");
    }
}
