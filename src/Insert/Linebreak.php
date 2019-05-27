<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;

final class Linebreak implements InsertInterface
{
    /**
     * @var InsertInterface
     */
    private $insert;

    /**
     * @var Delta
     */
    private $delta;

    /**
     * @return string
     */
    public function html(): string
    {
        return '<br>';
    }

    /**
     * @param InsertInterface $insert
     * @return InsertInterface
     */
    public function withInsert(InsertInterface $insert): InsertInterface
    {
        $item = clone $this;
        $item->insert = $insert;

        return $item;
    }

    /**
     * @param Delta $delta
     * @return InsertInterface
     */
    public function withDelta(Delta $delta): InsertInterface
    {
        $item = clone $this;
        $item->delta = $delta;

        return $item;
    }

    /**
     * @param Delta $delta
     * @return bool
     */
    public function isResponsible(Delta $delta): bool
    {
        return
            \is_string($delta->insert())
            && $delta->insert() === "\n"
            && \is_array($delta->attributes([]))
            && \array_key_exists('linebreak', $delta->attributes([]))
            && $delta->attributes([])['linebreak'] === true
            ;
    }
}
