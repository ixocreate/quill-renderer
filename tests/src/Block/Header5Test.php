<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Block\Header5;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Block\Header5
 */
final class Header5Test extends TestCase
{
    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Header5();

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
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['header' => 5]]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testAddImmutable()
    {
        $block = new Header5();
        $newBlock = $block->add($this->createMock(InsertInterface::class));

        $this->assertNotSame($newBlock, $block);
        $this->assertInstanceOf(Header5::class, $newBlock);
    }

    public function testAccept()
    {
        $block = new Header5();

        $this->assertTrue($block->accept());
        $this->assertTrue($block->accept($this->createMock(BlockInterface::class)));
    }

    public function testHtml()
    {
        $block = new Header5();
        $this->assertSame('<h5></h5>', $block->html());

        $block = $block->add((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertSame('<h5>test</h5>', $block->html());
    }
}
