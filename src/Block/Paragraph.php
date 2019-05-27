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

final class Paragraph implements BlockInterface
{
    /**
     * @var InsertInterface[]
     */
    private $inserts = [];

    /**
     * @param InsertInterface $insert
     * @return BlockInterface
     */
    public function add(InsertInterface $insert): BlockInterface
    {
        $block = clone $this;
        $block->inserts[] = $insert;

        return $block;
    }

    /**
     * @param Delta $delta
     * @return bool
     */
    public function isResponsible(Delta $delta): bool
    {
        return $delta->insert() === "\n" && empty($delta->attributes());
    }

    /**
     * @param BlockInterface|null $currentBlock
     * @return bool
     */
    public function accept(BlockInterface $currentBlock = null): bool
    {
        return true;
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

        return \sprintf('<p>%s</p>', $html);
    }
}
