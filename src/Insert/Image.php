<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;

final class Image implements InsertInterface
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
        if (empty($this->delta)) {
            return '';
        }

        if (empty($this->delta->insert()) || !\is_array($this->delta->insert())) {
            return '';
        }

        if (!\array_key_exists('image', $this->delta->insert())) {
            return '';
        }

        return \sprintf('<img src="%s">', $this->delta->insert()['image']);
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
        return \is_array($delta->insert()) && \array_key_exists('image', $delta->insert()) && \is_string($delta->insert()['image']);
    }
}
