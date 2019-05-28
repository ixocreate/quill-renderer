<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Block\Header1;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Block\Header1
 */
final class Header1Test extends TestCase
{
    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Header1();

        $this->assertSame($isResponsible, $insert->isResponsible($delta));
    }

    public function responsibleProvider()
    {
        return [
            [
                'delta' => new Delta([]),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['attributes' => 'string']),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['attributes' => []]),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['attributes' => ['something' => 'else']]),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['attributes' => ['header' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['header' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['header' => 1]]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testFinishImmutable()
    {
        $block = new Header1();
        $newBlock = $block->finish();
        $this->assertNotSame($newBlock, $block);

        $block = new Header1();
        $newBlock = $block->finish((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertNotSame($newBlock, $block);
    }

    public function testHtml()
    {
        $block = new Header1();
        $this->assertSame('<h1></h1>', $block->html());

        $block = new Header1();
        $block = $block->finish((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertSame('<h1>test</h1>', $block->html());
    }
}
