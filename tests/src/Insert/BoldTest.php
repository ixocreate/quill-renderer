<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Bold;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Bold
 */
final class BoldTest extends TestCase
{
    public function testWithInsert()
    {
        $insertMock = $this->createMock(InsertInterface::class);
        $insert = new Bold();
        $newInsert = $insert->withInsert($insertMock);

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Bold::class, $newInsert);
    }

    public function testWithDelta()
    {
        $insert = new Bold();
        $newInsert = $insert->withDelta(new Delta([]));

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Bold::class, $newInsert);
    }

    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Bold();

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
                'delta' => new Delta(['attributes' => ['bold' => true]]),
                'isResponsible' => true,
            ],
            [
                'delta' => new Delta(['attributes' => ['bold' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['bold' => 1]]),
                'isResponsible' => false,
            ],
        ];
    }

    public function testHtml()
    {
        $bold = new Bold();

        $this->assertSame('', $bold->html());
        $this->assertSame('<b>Test</b>', $bold->withInsert((new Insert())->withDelta(new Delta(['insert' => 'Test'])))->html());
    }
}
