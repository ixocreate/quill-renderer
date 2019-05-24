<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer;

use Ixocreate\QuillRenderer\Block\Block;
use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Block\Header1;
use Ixocreate\QuillRenderer\Block\Header2;
use Ixocreate\QuillRenderer\Block\Header3;
use Ixocreate\QuillRenderer\Block\Header4;
use Ixocreate\QuillRenderer\Block\Header5;
use Ixocreate\QuillRenderer\Block\Header6;
use Ixocreate\QuillRenderer\Block\OrderedList;
use Ixocreate\QuillRenderer\Block\Paragraph;
use Ixocreate\QuillRenderer\Block\UnorderedList;
use Ixocreate\QuillRenderer\Insert\Bold;
use Ixocreate\QuillRenderer\Insert\Image;
use Ixocreate\QuillRenderer\Insert\Insert;
use Ixocreate\QuillRenderer\Insert\InsertInterface;
use Ixocreate\QuillRenderer\Insert\Italic;
use Ixocreate\QuillRenderer\Insert\Link;
use Ixocreate\QuillRenderer\Insert\Strike;
use Ixocreate\QuillRenderer\Insert\Subscript;
use Ixocreate\QuillRenderer\Insert\Superscript;
use Ixocreate\QuillRenderer\Insert\Underline;

final class Renderer
{
    /**
     * @var array
     */
    private $ops = [];

    /**
     * @var Block
     */
    private $block;

    /**
     * @var Insert
     */
    private $insert;

    /**
     * @var BlockInterface[]
     */
    private $blocks = [];

    /**
     * Renderer constructor.
     */
    public function __construct()
    {
        $this->insert = new Insert();
        $this->block = new Block();
    }

    /**
     *
     */
    public function enableDefaults(): void
    {
        $this->addBlock(new Paragraph());
        $this->addBlock(new Header1());
        $this->addBlock(new Header2());
        $this->addBlock(new Header3());
        $this->addBlock(new Header4());
        $this->addBlock(new Header5());
        $this->addBlock(new Header6());
        $this->addBlock(new OrderedList());
        $this->addBlock(new UnorderedList());

        $this->addInsert(new Bold());
        $this->addInsert(new Italic());
        $this->addInsert(new Strike());
        $this->addInsert(new Subscript());
        $this->addInsert(new Superscript());
        $this->addInsert(new Underline());
        $this->addInsert(new Link());
        $this->addInsert(new Image());
    }

    /**
     * @param InsertInterface $insert
     */
    public function addInsert(InsertInterface $insert): void
    {
        $this->insert = $this->insert->addSupportingInsert($insert);
    }

    /**
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block): void
    {
        $this->block = $this->block->addSupportingBlock($block);
    }

    /**
     *
     */
    private function reset(): void
    {
        $this->ops = [];
        $this->blocks = [];
    }

    /**
     * @param array $quill
     * @return string
     */
    public function render(array $quill): string
    {
        $this->reset();

        $this->ops = $quill['ops'];
        $this->parse();

        return $this->generateHtml();
    }

    /**
     *
     */
    private function prepare(): void
    {
        $ops = [];

        foreach ($this->ops as $delta) {
            if (!\array_key_exists('insert', $delta)) {
                continue;
            }

            if (!\is_string($delta['insert'])) {
                $ops[] = new Delta($delta);

                continue;
            }

            if ($delta['insert'] === "\n") {
                $ops[] = new Delta($delta);

                continue;
            }

            $insert = $delta['insert'];
            $attributes = (!empty($delta['attributes'])) ? $delta['attributes'] : null;
            while (\preg_match("/[\n]/", $insert) === 1) {
                $tmp = \mb_substr($insert, 0, \mb_strpos($insert, "\n"));
                if (!empty($tmp)) {
                    $ops[] = new Delta(['insert' => $tmp, 'attributes' => $attributes]);
                }

                $ops[] = new Delta(['insert' => "\n"]);
                $insert = \mb_substr($insert, \mb_strpos($insert, "\n") + \mb_strlen("\n"));
            }

            if (!empty($insert)) {
                $ops[] = new Delta(['insert' => $insert, 'attributes' => $attributes]);
            }
        }

        $this->ops = $ops;
    }

    /**
     *
     */
    private function parse(): void
    {
        $this->prepare();

        $ops = \array_reverse($this->ops);

        /** @var Block $currentBlock */
        $currentBlock = $this->block;
        $collection = [];

        foreach ($ops as $delta) {
            $responsible = $currentBlock->getResponsible($delta);
            if ($responsible !== null) {
                if ($currentBlock->accept($responsible)) {
                    $this->newBlock($currentBlock, $collection);
                    $collection = [];
                    $currentBlock = $this->block->withDelta($delta);
                }
                continue;
            }

            $collection[] = $this->insert->withDelta($delta);
        }

        if (!empty($collection)) {
            $this->newBlock($currentBlock, $collection);
        }
    }

    /**
     * @param BlockInterface $block
     * @param array $collection
     */
    private function newBlock(BlockInterface $block, array $collection): void
    {
        foreach ($collection as $item) {
            $block = $block->add($item);
        }
        \array_unshift($this->blocks, $block);
    }

    /**
     * @return string
     */
    private function generateHtml(): string
    {
        $html = '';

        foreach ($this->blocks as $block) {
            $html .= $block->html();
        }

        return $html;
    }
}
