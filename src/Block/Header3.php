<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Delta;
use Ixocreate\QuillRenderer\Insert\InsertInterface;

final class Header3 implements BlockInterface
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
        return
            $delta->insert() === "\n"
            && \is_array($delta->attributes())
            && \array_key_exists('header', $delta->attributes())
            && $delta->attributes()['header'] === 3
        ;
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

        return \sprintf('<h3>%s</h3>', $html);
    }
}
