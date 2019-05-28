<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Block\OrderedList;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Block\OrderedList
 */
final class OrderedListTest extends TestCase
{
    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new OrderedList();

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
                'delta' => new Delta(['attributes' => ['list' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['list' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['list' => 'ordered']]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testAccept()
    {
        $block = new OrderedList();

        $this->assertFalse($block->accept($this->createMock(BlockInterface::class)));
        $this->assertTrue($block->accept(new OrderedList()));
    }

    public function testFinishImmutable()
    {
        $block = new OrderedList();
        $newBlock = $block->finish();
        $this->assertNotSame($newBlock, $block);

        $block = new OrderedList();
        $newBlock = $block->finish((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertNotSame($newBlock, $block);
    }

    public function testCompoundImmutable()
    {
        $block = new OrderedList();
        $newBlock = $block->compound();
        $this->assertNotSame($newBlock, $block);

        $block = new OrderedList();
        $newBlock = $block->compound((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertNotSame($newBlock, $block);
    }

    public function testHtml()
    {
        $block = new OrderedList();
        $this->assertSame('<ol></ol>', $block->html());

        $block = new OrderedList();
        $block = $block->compound((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertSame('<ol><li>test</li></ol>', $block->html());

        $block = new OrderedList();
        $block = $block->compound((new Insert())->withDelta(new Delta(['insert' => 'test2'])));
        $block = $block->compound((new Insert())->withDelta(new Delta(['insert' => 'test1'])));
        $this->assertSame('<ol><li>test1</li><li>test2</li></ol>', $block->html());
    }
}
