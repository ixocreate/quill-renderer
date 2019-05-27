<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;

final class Italic implements InsertInterface
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
        if (empty($this->insert)) {
            return '';
        }

        return \sprintf('<i>%s</i>', $this->insert->html());
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
            \is_array($delta->attributes([]))
            && \array_key_exists('italic', $delta->attributes([]))
            && $delta->attributes([])['italic'] === true
        ;
    }
}
