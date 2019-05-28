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
use Ixocreate\QuillRenderer\Insert\Linebreak;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Linebreak
 */
final class LinebreakTest extends TestCase
{
    public function testWithInsert()
    {
        $insertMock = $this->createMock(InsertInterface::class);
        $insert = new Linebreak();
        $newInsert = $insert->withInsert($insertMock);

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Linebreak::class, $newInsert);
    }

    public function testWithDelta()
    {
        $insert = new Linebreak();
        $newInsert = $insert->withDelta(new Delta([]));

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Linebreak::class, $newInsert);
    }

    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Linebreak();

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
                'delta' => new Delta(['attributes' => ['linebreak' => 'else']]),
                'isResponsible' => false,
            ],

            [
                'delta' => new Delta(['insert' => "\n", 'attributes' => ['linebreak' => true]]),
                'isResponsible' => true,
            ],
            [
                'delta' => new Delta(['insert' => "\nsdfsdfsd", 'attributes' => ['linebreak' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['insert' => [], 'attributes' => ['linebreak' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['linebreak' => true]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['linebreak' => false]]),
                'isResponsible' => false,
            ],
            [
                'delta' => new Delta(['attributes' => ['linebreak' => 1]]),
                'isResponsible' => false,
            ],
        ];
    }

    public function testHtml()
    {
        $italic = new Linebreak();

        $this->assertSame('<br>', $italic->html());
        $this->assertSame('<br>', $italic->withInsert((new Insert())->withDelta(new Delta(['insert' => 'Test'])))->html());
    }
}
