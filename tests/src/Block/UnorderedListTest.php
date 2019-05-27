<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Block\UnorderedList;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Block\UnorderedList
 */
final class UnorderedListTest extends TestCase
{
    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new UnorderedList();

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
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['list' => 'bullet']]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testAddImmutable()
    {
        $block = new UnorderedList();
        $newBlock = $block->add($this->createMock(InsertInterface::class));

        $this->assertNotSame($newBlock, $block);
        $this->assertInstanceOf(UnorderedList::class, $newBlock);
    }

    public function testAccept()
    {
        $block = new UnorderedList();

        $this->assertTrue($block->accept());
        $this->assertTrue($block->accept($this->createMock(BlockInterface::class)));
        $this->assertFalse($block->accept(new UnorderedList()));
    }

    public function testHtml()
    {
        $block = new UnorderedList();
        $this->assertSame('<ul></ul>', $block->html());

        $block = $block->add((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertSame('<ul><li>test</li></ul>', $block->html());
    }
}
