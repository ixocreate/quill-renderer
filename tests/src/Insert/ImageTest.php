<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Image;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Image
 */
final class ImageTest extends TestCase
{
    public function testWithInsert()
    {
        $insertMock = $this->createMock(InsertInterface::class);
        $insert = new Image();
        $newInsert = $insert->withInsert($insertMock);

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Image::class, $newInsert);
    }

    public function testWithDelta()
    {
        $insert = new Image();
        $newInsert = $insert->withDelta(new Delta([]));

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Image::class, $newInsert);
    }

    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Image();

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
                'delta' => new Delta(['insert' => 'string']),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['insert' => []]),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['insert' => ['something' => 'else']]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => ['image' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => ['image' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => ['image' => 1]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => ['image' => "data:image/png;base64,base64encodedstring"]]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testHtml()
    {
        $image = new Image();

        $this->assertSame('', $image->html());
        $this->assertSame('', $image->withInsert((new Insert())->withDelta(new Delta(['insert' => 'Test'])))->html());

        $this->assertSame('', $image->withDelta(new Delta([]))->html());
        $this->assertSame('', $image->withDelta(new Delta(['insert' => 'string']))->html());
        $this->assertSame('', $image->withDelta(new Delta(['insert' => ['foo' => 'bar']]))->html());
        $this->assertSame('<img src="data:image/png;base64,base64encodedstring">', $image->withDelta(new Delta(['insert' => ['image' => 'data:image/png;base64,base64encodedstring']]))->html());
    }
}
