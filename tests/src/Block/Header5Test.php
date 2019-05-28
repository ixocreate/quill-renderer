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

    public function testFinishImmutable()
    {
        $block = new Header5();
        $newBlock = $block->finish();
        $this->assertNotSame($newBlock, $block);

        $block = new Header5();
        $newBlock = $block->finish((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertNotSame($newBlock, $block);
    }

    public function testHtml()
    {
        $block = new Header5();
        $this->assertSame('<h5></h5>', $block->html());

        $block = new Header5();
        $block = $block->finish((new Insert())->withDelta(new Delta(['insert' => 'test'])));
        $this->assertSame('<h5>test</h5>', $block->html());
    }
}
