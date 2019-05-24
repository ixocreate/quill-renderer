<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer\Insert;

use BadMethodCallException;
use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\Bold;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\Italic;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\QuillRenderer\Insert\Insert
 */
class InsertTest extends TestCase
{
    /**
     * @dataProvider responsibleProvider
     */
    public function testIsResponsible(Delta $delta, bool $isResponsible)
    {
        $insert = new Insert();

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
                'isResponsible' => false,
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

    public function testWithInsert()
    {
        $this->expectException(BadMethodCallException::class);
        $insert = new Insert();

        $insert->withInsert(new Insert());
    }

    public function testAddSupportingInsertImmutable()
    {
        $insert = new Insert();
        $newInsert = $insert->addSupportingInsert(new Insert());

        $this->assertNotSame($newInsert, $insert);
        $this->assertInstanceOf(Insert::class, $newInsert);
    }

    public function testHtml()
    {
        $insert = new Insert();

        $this->assertSame('', $insert->html());
        $this->assertSame('', $insert->withDelta(new Delta(['insert' => []]))->html());

        $string = '<>Test"!!!&';

        $this->assertSame(\htmlspecialchars($string, ENT_QUOTES, 'UTF-8'), $insert->withDelta(new Delta(['insert' => $string]))->html());
    }

    public function testNestedInserts()
    {
        $insert = new Insert();
        $insert = $insert->addSupportingInsert(new Bold());
        $insert = $insert->addSupportingInsert(new Italic());

        $string = '<>Test"!!!&';
        $delta = new Delta(['insert' => $string, 'attributes' => ['bold' => true]]);
        $this->assertSame('<b>' . \htmlspecialchars($string, ENT_QUOTES, 'UTF-8') . '</b>', $insert->withDelta($delta)->html());
    }
}
