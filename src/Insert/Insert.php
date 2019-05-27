<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Insert;

use Ixocreate\QuillRenderer\Delta;

final class Insert implements InsertInterface
{
    /**
     * @var Delta
     */
    private $delta;

    /**
     * @var InsertInterface[]
     */
    private $supportingInserts = [];

    /**
     * Insert constructor.
     */
    public function __construct()
    {
        $this->delta = new Delta([]);
    }

    /**
     * @param InsertInterface $insert
     * @return Insert
     */
    public function addSupportingInsert(InsertInterface $insert): Insert
    {
        $cloned = clone $this;
        $cloned->supportingInserts[] = $insert;

        return $cloned;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        if (!\is_string($this->delta->insert(""))) {
            return '';
        }

        return \htmlspecialchars($this->delta->insert(""), ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param Delta $delta
     * @return InsertInterface
     */
    public function withDelta(Delta $delta): InsertInterface
    {
        $insert = clone $this;
        $insert->delta = $delta;

        foreach ($this->supportingInserts as $item) {
            if (!$item->isResponsible($delta)) {
                continue;
            }

            $insert = $item->withInsert($insert);
            $insert = $insert->withDelta($delta);
        }

        return $insert;
    }

    /**
     * @param InsertInterface $insert
     * @return InsertInterface
     */
    public function withInsert(InsertInterface $insert): InsertInterface
    {
        throw new \BadMethodCallException(
            \sprintf("'%s' is the most inner container", Insert::class)
        );
    }

    /**
     * @param Delta $delta
     * @return bool
     */
    public function isResponsible(Delta $delta): bool
    {
        return false;
    }
}
