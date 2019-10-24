<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer;

use Ixocreate\QuillRenderer\Block\BlockInterface;
use Ixocreate\QuillRenderer\Block\CompoundInterface;
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
use Ixocreate\QuillRenderer\Insert\Linebreak;
use Ixocreate\QuillRenderer\Insert\Link;
use Ixocreate\QuillRenderer\Insert\Strike;
use Ixocreate\QuillRenderer\Insert\Subscript;
use Ixocreate\QuillRenderer\Insert\Superscript;
use Ixocreate\QuillRenderer\Insert\Underline;

final class Renderer
{
    /**
     * @var BlockInterface[]
     */
    private $supportingBlocks = [];

    /**
     * @var array
     */
    private $ops = [];

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
        $this->addInsert(new Linebreak());
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
        $this->supportingBlocks[] = $block;
    }

    /**
     * @param Delta $delta
     * @return BlockInterface|null
     */
    private function getResponsible(Delta $delta): ?BlockInterface
    {
        foreach ($this->supportingBlocks as $block) {
            if ($block->isResponsible($delta)) {
                return $block;
            }
        }
        return null;
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

        if (empty($quill['ops']) || !\is_array($quill['ops'])) {
            return '';
        }

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

        // Fix deltas with none ending slash delta
        /** @var Delta $last */
        $last = \end($ops);
        if ($last->insert() !== "\n") {
            $ops[] = new Delta(['insert' => "\n"]);
        }

        $ops = \array_reverse($ops);

        $replace = false;
        $prevIsParagraph = null;
        /** @var Delta $delta */
        foreach ($ops as $key => $delta) {
            if ($delta->insert() !== "\n") {
                $prevIsParagraph = false;
                continue;
            }

            if ((new Linebreak())->isResponsible($delta)) {
                if ($prevIsParagraph === true) {
                    unset($ops[$key]);
                } elseif ($replace === false) {
                    $ops[$key] = new Delta(['insert' => "\n"]);
                }

                $replace = true;
                $prevIsParagraph = false;
                continue;
            }

            if ((new Paragraph())->isResponsible($delta) && $replace === true) {
                $ops[$key] = new Delta(['insert' => "\n", 'attributes' => ['linebreak' => true]]);
            } elseif ((new Paragraph())->isResponsible($delta)) {
                $prevIsParagraph = true;
            }

            $replace = false;
        }

        $this->ops = \array_reverse($ops);
    }

    /**
     *
     */
    private function parse(): void
    {
        $this->prepare();

        $ops = \array_reverse($this->ops);

        /** @var BlockInterface $currentBlock */
        $currentBlock = null;
        $collection = [];

        foreach ($ops as $delta) {
            $responsible = $this->getResponsible($delta);
            if ($responsible === null) {
                $collection[] = $this->insert->withDelta($delta);
                continue;
            }

            if ($currentBlock === null) {
                $currentBlock = $responsible;
                continue;
            }

            if (!($currentBlock instanceof CompoundInterface)) {
                \array_unshift(
                    $this->blocks,
                    $currentBlock->finish(...\array_reverse($collection))
                );
                $collection = [];
                $currentBlock = $responsible;
                continue;
            }

            if (!$currentBlock->accept($responsible)) {
                \array_unshift(
                    $this->blocks,
                    $currentBlock->finish(...\array_reverse($collection))
                );
                $collection = [];
                $currentBlock = $responsible;
                continue;
            }

            $currentBlock = $currentBlock->compound(...\array_reverse($collection));
            $collection = [];
        }

        if (!empty($collection) && !empty($currentBlock)) {
            \array_unshift(
                $this->blocks,
                $currentBlock->finish(...\array_reverse($collection))
            );
        }
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
