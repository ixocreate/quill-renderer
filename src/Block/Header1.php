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

final class Header1 implements BlockInterface
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
            && \array_key_exists('header', $delta->attributes())
            && $delta->attributes()['header'] === 1
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

        return \sprintf('<h1>%s</h1>', $html);
    }
}
