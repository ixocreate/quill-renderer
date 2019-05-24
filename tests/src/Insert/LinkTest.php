<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use Ixocreate\QuillRenderer\Insert\Link;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Link
 */
final class LinkTest extends TestCase
{
    public function testWithInsert()
    {
        $insertMock = $this->createMock(InsertInterface::class);
        $insert = new Link();
        $newInsert = $insert->withInsert($insertMock);

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Link::class, $newInsert);
    }

    public function testWithDelta()
    {
        $insert = new Link();
        $newInsert = $insert->withDelta(new Delta([]));

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Link::class, $newInsert);
    }

    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Link();

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
                'delta' => new Delta(['attributes' => ['link' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['link' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['link' => 1]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['link' => "https://www.ixocreate.com"]]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testHtml()
    {
        $link = new Link();

        $this->assertSame('', $link->html());
        $link = $link->withInsert((new Insert())->withDelta(new Delta(['insert' => 'Test'])));
        $this->assertSame('', $link->html());
        $link = $link->withDelta(new Delta(['attributes' => ['link' => null]]));
        $this->assertSame('', $link->html());

        $link = $link->withDelta(new Delta(['attributes' => ['link' => 'https://www.ixocreate.com']]));
        $this->assertSame('<a target="_blank" href="https://www.ixocreate.com">Test</a>', $link->html());
    }
}
