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
use Ixocreate\QuillRenderer\Insert\Underline;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Underline
 */
final class UnderlineTest extends TestCase
{
    public function testWithInsert()
    {
        $insertMock = $this->createMock(InsertInterface::class);
        $insert = new Underline();
        $newInsert = $insert->withInsert($insertMock);

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Underline::class, $newInsert);
    }

    public function testWithDelta()
    {
        $insert = new Underline();
        $newInsert = $insert->withDelta(new Delta([]));

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Underline::class, $newInsert);
    }

    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Underline();

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
                'delta' => new Delta(['attributes' => ['underline' => true]]),
                'isResponsible' => true,
            ],
            [
                'delta' => new Delta(['attributes' => ['underline' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['underline' => 1]]),
                'isResponsible' => false,
            ],
        ];
    }

    public function testHtml()
    {
        $underline = new Underline();

        $this->assertSame('', $underline->html());
        $this->assertSame('<u>Test</u>', $underline->withInsert((new Insert())->withDelta(new Delta(['insert' => 'Test'])))->html());
    }
}
