<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;

final class Superscript implements InsertInterface
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

        return \sprintf('<sup>%s</sup>', $this->insert->html());
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
        $isResponsible = \is_array($delta->attributes([]))
            && \array_key_exists('super', $delta->attributes([]))
            && $delta->attributes([])['super'] === true;

        if (!$isResponsible) {
            $isResponsible = \is_array($delta->attributes([]))
                && \array_key_exists('script', $delta->attributes([]))
                && $delta->attributes([])['script'] === 'super';
        }

        return $isResponsible;
    }
}
