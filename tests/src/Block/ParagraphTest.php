<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Block\Paragraph;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Block\Paragraph
 */
final class ParagraphTest extends TestCase
{
    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Paragraph();

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
                'delta' => new Delta(['insert' => "\n", 'attributes' => []]),
                'isResponsible' => true,
            ],

            [
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['something' => 'else']]),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['insert' => "\n",'attributes' => ['header' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['header' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => "\n"]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testAddImmutable()
    {
        $block = new Paragraph();
        $newBlock = $block->add($this->createMock(InsertInterface::class));

        $this->assertNotSame($newBlock, $block);
        $this->assertInstanceOf(Paragraph::class, $newBlock);
    }

    public function testAccept()
    {
        $block = new Paragraph();

        $this->assertTrue($block->accept());
        $this->assertTrue($block->accept($this->createMock(BlockInterface::class)));
    }

    public function testHtml()
    {
        $block = new Paragraph();
        $this->assertSame('<p></p>', $block->html());

        $block = $block->add((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertSame('<p>test</p>', $block->html());
    }
}
