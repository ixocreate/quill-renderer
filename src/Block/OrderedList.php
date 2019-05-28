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

final class OrderedList implements BlockInterface, CompoundInterface
{
    /**
     * @var array
     */
    private $inserts = [];

    /**
     * @var int
     */
    private $key = 0;

    /**
     * @param InsertInterface ...$inserts
     * @return BlockInterface
     */
    public function finish(InsertInterface ...$inserts): BlockInterface
    {
        $block = clone $this;

        if (!\array_key_exists($this->key, $block->inserts)) {
            $block->inserts[$this->key] = [];
        }

        foreach ($inserts as $insert) {
            $block->inserts[$this->key][] = $insert;
        }

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
     * @param BlockInterface $block
     * @return bool
     */
    public function accept(BlockInterface $block): bool
    {
        if ($block instanceof OrderedList) {
            return true;
        }

        return false;
    }

    /**
     * @param InsertInterface[] $inserts
     * @return BlockInterface
     */
    public function compound(InsertInterface ...$inserts): BlockInterface
    {
        /** @var OrderedList $block */
        $block = $this->finish(...$inserts);
        $block->key++;

        return $block;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        $html = '';

        foreach (\array_reverse($this->inserts) as $inserts) {
            $html .= '<li>' . $this->renderListItem($inserts) . '</li>';
        }

        return \sprintf('<ol>%s</ol>', $html);
    }

    private function renderListItem(array $inserts): string
    {
        $html = '';

        /** @var InsertInterface $insert */
        foreach ($inserts as $insert) {
            $html .= $insert->html();
        }

        return  $html;
    }
}
