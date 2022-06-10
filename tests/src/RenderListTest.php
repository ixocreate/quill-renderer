<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\QuillRenderer;

use Ixocreate\QuillRenderer\Renderer;
use PHPUnit\Framework\TestCase;

class RenderListTest extends TestCase
{
    /**
     * @dataProvider getDeltas
     * @param array $deltas
     * @param string $html
     */
    public function testListRendering(array $deltas, string $html)
    {
        $renderer = new Renderer();
        $renderer->enableDefaults();

        $this->assertSame($html, $renderer->render($deltas));
    }

    public function getDeltas()
    {
        return [
            $this->generateTestCase([0, 0, 0]),
            $this->generateTestCase([0, 1, 1]),
            $this->generateTestCase([0, 1, 2]),
            $this->generateTestCase([0, 1, 2, 0]),
            $this->generateTestCase([0, 1, 2, 0, 0]),
            $this->generateTestCase([0, 1, 2, 2, 0, 0]),
            $this->generateTestCase([0, 0, 1, 2, 2, 0, 0, 1, 2, 3, 3, 4, 0]),
        ];
    }

    private function generateTestCase(array $map): array
    {
        $deltas = [];

        $i = 1;
        foreach ($map as $depth) {
            $attributes = ['list' => 'ordered'];
            if ($depth > 0) {
                $attributes['indent'] = $depth;
            }

            $deltas[] = ['insert' => 'Element' . $i++];
            $deltas[] = ['insert' => "\n", 'attributes' => $attributes];
        }


        $html = '';
        $i = 1;
        $level = -1;
        $lastOpen = -1;
        foreach ($map as $k => $depth) {
            if ($depth > $level) {
                $html .= '<ol>';
                $lastOpen = $depth;
            }

            $level = $depth;

            $html .= '<li>Element' . $i++;

            if (\array_key_exists($k + 1, $map)) {
                if ($map[$k + 1] < $depth) {
                    $html .= \str_repeat('</li></ol>', $lastOpen - $map[$k + 1]);
                    $html .= '</li>';
                } elseif ($map[$k + 1] === $depth) {
                    $html .= '</li>';
                }
            }
        }
        $html .= \str_repeat('</li></ol>', $depth + 1);

        return [['ops' => $deltas], $html];
    }
}
