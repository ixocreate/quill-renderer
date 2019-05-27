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

final class UnorderedList implements BlockInterface
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
            && $delta->attributes()['list'] === 'bullet'
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
        if ($currentBlock instanceof UnorderedList) {
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

        return \sprintf('<ul>%s</ul>', $html);
    }
}
