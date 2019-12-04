<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\InsertInterface;

final class ParagraphJustify implements BlockInterface
{
    /**
     * @var InsertInterface[]
     */
    private $inserts = [];

    /**
     * @param InsertInterface ...$inserts
     * @return BlockInterface
     */
    public function finish(InsertInterface ...$inserts): BlockInterface
    {
        $block = clone $this;
        foreach ($inserts as $insert) {
            $block->inserts[] = $insert;
        }

        return $block;
    }

    /**
     * @param Delta $delta
     * @return bool
     */
    public function isResponsible(Delta $delta): bool
    {
        return
            $delta->insert() === "\n"
            && \is_array($delta->attributes())
            && \array_key_exists('align', $delta->attributes())
            && $delta->attributes()['align'] === "justify"
        ;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        $html = '';

        foreach ($this->inserts as $insert) {
            $html .= $insert->html();
        }

        return \sprintf('<p class="ql-align-justify">%s</p>', $html);
    }
}
