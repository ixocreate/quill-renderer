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

/**
 * @123covers \Ixocreate\QuillRenderer\Renderer
 */
class RendererTest extends TestCase
{
    public function testEmptyDeltas()
    {
        $renderer = new Renderer();
        $renderer->enableDefaults();

        $this->assertSame('', $renderer->render([]));
        $this->assertSame('', $renderer->render(['ops' => null]));
        $this->assertSame('', $renderer->render(['ops' => 'something']));
    }

    /**
     * @dataProvider getDeltas
     * @param array $deltas
     * @param string $html
     */
    public function testRendering(array $deltas, string $html)
    {
        $renderer = new Renderer();
        $renderer->enableDefaults();

        $this->assertSame($html, $renderer->render($deltas));
    }

    public function getDeltas()
    {
        return [
            [
                'deltas' => [
                    'ops' => [
                        [
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "A single line\n",
                        ],
                    ],
                ],
                'html' => '<p>A single line</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Inline styles\n",
                            'attributes' => [
                                'bold' => true,
                                'italic' => true,
                                'underline' => true,
                                'link' => 'https://www.ixocreate.com',
                            ],
                        ],
                    ],
                ],
                'html' => '<p><a target="_blank" href="https://www.ixocreate.com"><u><i><b>Inline styles</b></i></u></a></p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Super",
                            'attributes' => [
                                'super' => true,
                            ],
                        ],
                        [
                            'insert' => "Sub",
                            'attributes' => [
                                'sub' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                        ],
                        [
                            'insert' => "Strike",
                            'attributes' => [
                                'strike' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => ['image' => "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACJUlEQVR4AY2TA4ykeRTE39i2bUfnC47B2rbGtm1PjLFt2zbjDtf27n9rbPyS7s+PVbQfF/MEuM9l80rfKhP9PXXRyhq/hJQFS0naD8cmeWnbetmrD6olo26XidVdKxLiXMzjZ2eyuJl3tzpLnDNjCbNmDXHTJry0F84tir4OjfLMulaa3auUYDdLRT5cKRAcOZ/Dm4FKnKMnjQaiJoxY5LhhWviYATdtxadHg9ujQ2UBQb6hissPqqUM71aIb8sUPqqvGDqsxwkZ1mXBQ7rWtBX/Pq2fl8t0b1dpoQMIGNC2xLtv/Po0vyDpv7RO8KBOmn+fJkOQm3QIXl1qxz06Vb+h4hdu7cqGFD1hxI/yngQN6rxDZHE6AmjV3alZkTk2K0wRpnp8eThhI/o5dERs6mT+x4/hV0JJc+YlCMIQ5H86IverJHLuY1M4niQIZAptsLNZPO50BG4UC4vfLBV9hzU/gV74KW3JytC1VenZuRzeb5fzBU7SIVwuELh+pUCA4ZdG6yD73xfz+L5Afe8gXUs6gHPZPE34MST8mbYC1VnfKBFhEBDnUY2UMm0BG+ING9EzDBvVv4zJf8G6F05ncG1X4/ViYe7b5WIp8AKDGgewKmePDtUM3x6NEQT4gFWzmEnjFU+kLFr6015gsrzwQsOyJzAXBtEwKI+FDOly4IG62GmTqKR586upS1bStB92DXKSKDMBKrNGqX/692tJQ/vctA8/ALBf+KIcFEv4AAAAAElFTkSuQmCC"],
                        ],
                        [
                            'insert' => "\n",
                        ],
                    ],
                ],
                'html' => '<p><sup>Super</sup><sub>Sub</sub><br><s>Strike</s></p><p><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACJUlEQVR4AY2TA4ykeRTE39i2bUfnC47B2rbGtm1PjLFt2zbjDtf27n9rbPyS7s+PVbQfF/MEuM9l80rfKhP9PXXRyhq/hJQFS0naD8cmeWnbetmrD6olo26XidVdKxLiXMzjZ2eyuJl3tzpLnDNjCbNmDXHTJry0F84tir4OjfLMulaa3auUYDdLRT5cKRAcOZ/Dm4FKnKMnjQaiJoxY5LhhWviYATdtxadHg9ujQ2UBQb6hissPqqUM71aIb8sUPqqvGDqsxwkZ1mXBQ7rWtBX/Pq2fl8t0b1dpoQMIGNC2xLtv/Po0vyDpv7RO8KBOmn+fJkOQm3QIXl1qxz06Vb+h4hdu7cqGFD1hxI/yngQN6rxDZHE6AmjV3alZkTk2K0wRpnp8eThhI/o5dERs6mT+x4/hV0JJc+YlCMIQ5H86IverJHLuY1M4niQIZAptsLNZPO50BG4UC4vfLBV9hzU/gV74KW3JytC1VenZuRzeb5fzBU7SIVwuELh+pUCA4ZdG6yD73xfz+L5Afe8gXUs6gHPZPE34MST8mbYC1VnfKBFhEBDnUY2UMm0BG+ING9EzDBvVv4zJf8G6F05ncG1X4/ViYe7b5WIp8AKDGgewKmePDtUM3x6NEQT4gFWzmEnjFU+kLFr6015gsrzwQsOyJzAXBtEwKI+FDOly4IG62GmTqKR586upS1bStB92DXKSKDMBKrNGqX/692tJQ/vctA8/ALBf+KIcFEv4AAAAAElFTkSuQmCC"></p>',
            ],


            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "One line\nSecond line\n",
                        ],
                    ],
                ],
                'html' => "<p>One line</p><p>Second line</p>",
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "One line\nSecond line\n",
                        ],
                        [
                            'insert' => "Bold",
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                        ],
                    ],
                ],
                'html' => "<p>One line</p><p>Second line</p><p><b>Bold</b></p>",
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "One ",
                        ],
                        [
                            'insert' => "line",
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\nSecond ",
                        ],
                        [
                            'insert' => "line",
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                        ],
                    ],
                ],
                'html' => "<p>One <b>line</b></p><p>Second <b>line</b></p>",
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "One line\nSecond line\n",
                        ],
                        [
                            'insert' => "BoldItalic",
                            'attributes' => [
                                'bold' => true,
                                'italic' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                        ],
                    ],
                ],
                'html' => "<p>One line</p><p>Second line</p><p><i><b>BoldItalic</b></i></p>",
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "h1",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 1,
                            ],
                        ],
                        [
                            'insert' => "h2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 2,
                            ],
                        ],
                        [
                            'insert' => "h3",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 3,
                            ],
                        ],
                        [
                            'insert' => "h4",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 4,
                            ],
                        ],
                        [
                            'insert' => "h5",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 5,
                            ],
                        ],
                        [
                            'insert' => "h6",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 6,
                            ],
                        ],
                        [
                            'insert' => "Normal Test\n",
                        ],
                    ],
                ],
                'html' => "<h1>h1</h1><h2>h2</h2><h3>h3</h3><h4>h4</h4><h5>h5</h5><h6>h6</h6><p>Normal Test</p>",
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "h1",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'header' => 1,
                            ],
                        ],
                        [
                            'insert' => 'List 1',
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'ordered',
                            ],
                        ],
                        [
                            'insert' => 'List 2',
                            'attributes' => [
                                'italic' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'ordered',
                            ],
                        ],
                        [
                            'insert' => 'List 3',
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'ordered',
                            ],
                        ],
                        [
                            'insert' => 'List B1',
                            'attributes' => [
                                'italic' => true,
                            ],
                        ],
                        [
                            'insert' => ' ',
                        ],
                        [
                            'insert' => 'List B1bold',
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => 'List B2',
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],

                    ],
                ],
                'html' => "<h1>h1</h1><ol><li><b>List 1</b></li><li><i>List 2</i></li><li>List 3</li></ol><ul><li><i>List B1</i> <b>List B1bold</b></li><li>List B2</li></ul>",
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Line1\nLine2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "Line3\nLine4",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "Line5\nLine6",
                        ],
                    ],
                ],
                'html' => '<p>Line1<br>Line2</p><p>Line3<br>Line4</p><p>Line5</p><p>Line6</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Line1\nList1",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "List2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "Line2",
                        ],
                    ],
                ],
                'html' => '<p>Line1</p><ul><li>List1</li><li>List2</li></ul><p>Line2</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "List1",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "Line1\nList2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "Line2",
                        ],
                    ],
                ],
                'html' => '<ul><li>List1</li></ul><p>Line1</p><ul><li>List2</li></ul><p>Line2</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Line1",
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "Line2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "Line3",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "\nLine4\n",
                        ],
                    ],
                ],
                'html' => '<p><b>Line1</b><br>Line2<br>Line3</p><p>Line4</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Line1",
                            'attributes' => [
                                'bold' => true,
                            ],
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "Line2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "Line3",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "\n\nLine4\n",
                        ],
                    ],
                ],
                'html' => '<p><b>Line1</b><br>Line2<br>Line3</p><p></p><p>Line4</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                                'header' => 1,
                            ],
                        ],
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                                'header' => 1,
                            ],
                        ],
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                                'header' => 1,
                            ],
                        ],
                    ],
                ],
                'html' => '<h1 class="ql-align-center">center</h1><h1 class="ql-align-right">right</h1><h1 class="ql-align-justify">justify</h1>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                                'header' => 2,
                            ],
                        ],
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                                'header' => 2,
                            ],
                        ],
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                                'header' => 2,
                            ],
                        ],
                    ],
                ],
                'html' => '<h2 class="ql-align-center">center</h2><h2 class="ql-align-right">right</h2><h2 class="ql-align-justify">justify</h2>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                                'header' => 3,
                            ],
                        ],
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                                'header' => 3,
                            ],
                        ],
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                                'header' => 3,
                            ],
                        ],
                    ],
                ],
                'html' => '<h3 class="ql-align-center">center</h3><h3 class="ql-align-right">right</h3><h3 class="ql-align-justify">justify</h3>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                                'header' => 4,
                            ],
                        ],
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                                'header' => 4,
                            ],
                        ],
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                                'header' => 4,
                            ],
                        ],
                    ],
                ],
                'html' => '<h4 class="ql-align-center">center</h4><h4 class="ql-align-right">right</h4><h4 class="ql-align-justify">justify</h4>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                                'header' => 5,
                            ],
                        ],
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                                'header' => 5,
                            ],
                        ],
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                                'header' => 5,
                            ],
                        ],
                    ],
                ],
                'html' => '<h5 class="ql-align-center">center</h5><h5 class="ql-align-right">right</h5><h5 class="ql-align-justify">justify</h5>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                                'header' => 6,
                            ],
                        ],
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                                'header' => 6,
                            ],
                        ],
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                                'header' => 6,
                            ],
                        ],
                    ],
                ],
                'html' => '<h6 class="ql-align-center">center</h6><h6 class="ql-align-right">right</h6><h6 class="ql-align-justify">justify</h6>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "center",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'center',
                            ],
                        ],
                    ],
                ],
                'html' => '<p class="ql-align-center">center</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "right",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'right',
                            ],
                        ],
                    ],
                ],
                'html' => '<p class="ql-align-right">right</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "justify",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'align' => 'justify',
                            ],
                        ],
                    ],
                ],
                'html' => '<p class="ql-align-justify">justify</p>',
            ],

            //Special case (just in case)
            [
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Line1\nList1",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "Line2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'linebreak' => true,
                            ],
                        ],
                        [
                            'insert' => "List2",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "List3",
                        ],
                        [
                            'insert' => "\n",
                            'attributes' => [
                                'list' => 'bullet',
                            ],
                        ],
                        [
                            'insert' => "Line3",
                        ],
                    ],
                ],
                'html' => '<p>Line1</p><ul><li>List1</li></ul><p>Line2</p><ul><li>List2</li><li>List3</li></ul><p>Line3</p>',
            ],

            [
                'deltas' => [
                    'ops' => [
                        [
                            "insert" => "1"
                        ],
                        [
                            "insert" => "\n",
                            "attributes" => [
                                "list" => "ordered"
                            ]
                        ],
                        [
                            "insert" => "s",
                            "attributes" => [
                                'bold' => true,
                            ]
                        ],
                        [
                            "insert" => "a"
                        ],
                        [
                            "insert" => "\n",
                            "attributes" => [
                                "list" => "ordered",
                                "indent" => 1
                            ]
                        ],
                        [
                            "insert" => "b"
                        ],
                        [
                            "insert" => "\n",
                            "attributes" => [
                                "list" => "ordered",
                                "indent" => 1
                            ]
                        ],
                        [
                            "insert" => "c"
                        ],
                        [
                            "insert" => "\n",
                            "attributes" => [
                                "list" => "ordered",
                                "indent" => 1
                            ]
                        ],
                        [
                            "insert" => "2"
                        ],
                        [
                            "insert" => "\n",
                            "attributes" => [
                                "list" => "ordered"
                            ]
                        ],
                        [
                            "insert" => "3"
                        ],
                        [
                            "insert" => "\n",
                            "attributes" => [
                                "list" => "ordered"
                            ]
                        ],
                    ],
                ],
                'html' => '<ol><li>1<ol><li><b>s</b>a</li><li>b</li><li>c</li></ol></li><li>2</li><li>3</li></ol>',
            ],

            /*[
                'deltas' => [
                    'ops' => [
                        [
                            'insert' => "Heading",
                        ],
                        [
                            'insert' => "\n\n",
                            'attributes' => [
                                'header' => '4',
                            ],
                        ],
                        [
                            'insert' => "Paragraph\n",
                        ],
                    ],
                ],
                'html' => '<h4>Heading</h4><h4><br></h4><p>Paragraph</p>',
            ],*/
        ];
    }
}
