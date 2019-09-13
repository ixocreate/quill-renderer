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
use Ixocreate\QuillRenderer\Insert\Strike;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Strike
 */
final class StrikeTest extends TestCase
{
    public function testWithInsert()
    {
        $insertMock = $this->createMock(InsertInterface::class);
        $insert = new Strike();
        $newInsert = $insert->withInsert($insertMock);

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Strike::class, $newInsert);
    }

    public function testWithDelta()
    {
        $insert = new Strike();
        $newInsert = $insert->withDelta(new Delta([]));

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Strike::class, $newInsert);
    }

    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Strike();

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
                'delta' => new Delta(['attributes' => ['strike' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['strike' => 1]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['strike' => true]]),
                'isResponsible' => true,
            ],
            [
                'delta' => new Delta(['attributes' => ['strike' => 'true']]),
                'isResponsible' => true,
            ],
        ];
    }

    public function testHtml()
    {
        $strike = new Strike();

        $this->assertSame('', $strike->html());
        $this->assertSame('<s>Test</s>', $strike->withInsert((new Insert())->withDelta(new Delta(['insert' => 'Test'])))->html());
    }
}
