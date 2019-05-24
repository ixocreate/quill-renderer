<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Block\Block;
use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Block\Block
 */
final class BlockTest extends TestCase
{
    public function testAddSupportingBlockImmutable()
    {
        $block = new Block();
        $newBlock = $block->addSupportingBlock(new Block());

        $this->assertNotSame($newBlock, $block);
        $this->assertInstanceOf(Block::class, $newBlock);
    }

    public function testAddInsertImmutable()
    {
        $block = new Block();
        $newBlock = $block->add($this->createMock(InsertInterface::class));

        $this->assertNotSame($newBlock, $block);
        $this->assertInstanceOf(Block::class, $newBlock);
    }

    public function testWithDeltaImmutable()
    {
        $block = new Block();
        $newBlock = $block->withDelta(new Delta([]));

        $this->assertNotSame($newBlock, $block);
        $this->assertInstanceOf(Block::class, $newBlock);
    }

    public function testAcceptWithoutBlock()
    {
        $block = new Block();
        $this->assertTrue($block->accept());
    }

    public function testSetBlockAndAccept()
    {
        $block1 = $this->createMock(BlockInterface::class);
        $block1->method('isResponsible')->willReturn(false);

        $block2 = $this->createMock(BlockInterface::class);
        $block2->method('isResponsible')->willReturn(true);
        $block2->method('accept')->willReturn(true);

        $block = new Block();
        $block = $block->addSupportingBlock($block1)->addSupportingBlock($block2);

        $newBlock = $block->withDelta(new Delta([]));
        $this->assertTrue($newBlock->accept());
    }

    public function testIsResponsible()
    {
        $block1 = $this->createMock(BlockInterface::class);
        $block1->method('isResponsible')->willReturn(false);
        $block2 = $this->createMock(BlockInterface::class);
        $block2->method('isResponsible')->willReturn(true);
        $block = new Block();
        $block = $block->addSupportingBlock($block1)->addSupportingBlock($block2);
        $this->assertTrue($block->isResponsible(new Delta([])));

        $block1 = $this->createMock(BlockInterface::class);
        $block1->method('isResponsible')->willReturn(false);
        $block2 = $this->createMock(BlockInterface::class);
        $block2->method('isResponsible')->willReturn(false);
        $block = new Block();
        $block = $block->addSupportingBlock($block1)->addSupportingBlock($block2);
        $this->assertFalse($block->isResponsible(new Delta([])));
    }

    public function testGetResponsible()
    {
        $block1 = $this->createMock(BlockInterface::class);
        $block1->method('isResponsible')->willReturn(false);
        $block2 = $this->createMock(BlockInterface::class);
        $block2->method('isResponsible')->willReturn(true);
        $block = new Block();
        $block = $block->addSupportingBlock($block1)->addSupportingBlock($block2);
        $this->assertSame($block2, $block->getResponsible(new Delta([])));

        $block1 = $this->createMock(BlockInterface::class);
        $block1->method('isResponsible')->willReturn(false);
        $block2 = $this->createMock(BlockInterface::class);
        $block2->method('isResponsible')->willReturn(false);
        $block = new Block();
        $block = $block->addSupportingBlock($block1)->addSupportingBlock($block2);
        $this->assertNull($block->getResponsible(new Delta([])));
    }

    public function testEmptyHtml()
    {
        $block = new Block();
        $this->assertSame("", $block->html());
    }

    public function testHtml()
    {
        $supportedBlock = new class() implements BlockInterface {
            private $insert = [];

            public function add(InsertInterface $insert): BlockInterface
            {
                $this->insert[] = $insert;
                return $this;
            }

            public function isResponsible(Delta $delta): bool
            {
                return true;
            }

            public function accept(BlockInterface $currentBlock = null): bool
            {
                return true;
            }

            public function html(): string
            {
                $html = '';

                foreach ($this->insert as $insert) {
                    $html .= $insert->html();
                }
                return $html;
            }
        };

        $block = new Block();
        $block = $block->addSupportingBlock($supportedBlock);
        $block = $block->withDelta(new Delta([]));
        $block = $block->add((new Insert())->withDelta(new Delta(['insert' => 'World'])));
        $block = $block->add((new Insert())->withDelta(new Delta(['insert' => 'Hello '])));

        $this->assertSame('Hello World', $block->html());
    }
}
