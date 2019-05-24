<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer;

use Ixocreate\QuillRenderer\Delta;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Delta
 */
class DeltaTest extends TestCase
{
    public function testDelta()
    {
        $delta = new Delta([]);
        $this->assertSame(null, $delta->insert());
        $this->assertSame('string', $delta->insert('string'));
        $this->assertSame(null, $delta->attributes());
        $this->assertSame('string', $delta->attributes('string'));

        $delta = new Delta(['insert' => null, 'attributes' => null]);
        $this->assertSame(null, $delta->insert());
        $this->assertSame('string', $delta->insert('string'));
        $this->assertSame(null, $delta->attributes());
        $this->assertSame('string', $delta->attributes('string'));

        $delta = new Delta(['insert' => 'testInsert', 'attributes' => 'testAttr']);
        $this->assertSame('testInsert', $delta->insert());
        $this->assertSame('testInsert', $delta->insert('string'));
        $this->assertSame('testAttr', $delta->attributes());
        $this->assertSame('testAttr', $delta->attributes('string'));
    }
}
