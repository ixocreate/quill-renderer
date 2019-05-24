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

final class OrderedList implements BlockInterface
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
        if (
            $delta->insert() === "\n"
            && \is_array($delta->attributes())
            && \array_key_exists('list', $delta->attributes())
            && $delta->attributes()['list'] === 'ordered'
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param BlockInterface|null $currentBlock
     * @return bool
     */
    public function accept(BlockInterface $currentBlock = null): bool
    {
        if ($currentBlock instanceof OrderedList) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        $html = '';

        foreach ($this->inserts as $insert) {
            $html .= '<li>' . $insert->html() . '</li>';
        }

        return \sprintf('<ol>%s</ol>', $html);
    }
}
