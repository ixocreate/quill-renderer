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

final class Block implements BlockInterface
{
    /**
     * @var BlockInterface[]
     */
    private $supportingBlocks = [];

    /**
     * @var array
     */
    private $inserts = [];

    /**
     * @var BlockInterface
     */
    private $block;

    /**
     * @param BlockInterface $block
     * @return Block
     */
    public function addSupportingBlock(BlockInterface $block): Block
    {
        $cloned = clone $this;
        $cloned->supportingBlocks[] = $block;

        return $cloned;
    }

    public function withDelta(Delta $delta): BlockInterface
    {
        $cloned = clone $this;

        foreach ($this->supportingBlocks as $block) {
            if (!$block->isResponsible($delta)) {
                continue;
            }

            if ($block->accept($cloned->block)) {
                $cloned->block = $block;
            }

            break;
        }

        return $cloned;
    }

    public function add(InsertInterface $insert): BlockInterface
    {
        $block = clone $this;
        \array_unshift($block->inserts, $insert);

        return $block;
    }

    public function isResponsible(Delta $delta): bool
    {
        foreach ($this->supportingBlocks as $block) {
            if ($block->isResponsible($delta)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Delta $delta
     * @return BlockInterface|null
     */
    public function getResponsible(Delta $delta): ?BlockInterface
    {
        foreach ($this->supportingBlocks as $block) {
            if ($block->isResponsible($delta)) {
                return $block;
            }
        }
        return null;
    }

    public function accept(BlockInterface $currentBlock = null): bool
    {
        if ($this->block === null) {
            return true;
        }

        return $this->block->accept($currentBlock);
    }

    public function html(): string
    {
        if (empty($this->block)) {
            return "";
        }

        $block = $this->block;
        foreach ($this->inserts as $insert) {
            $block = $block->add($insert);
        }

        return $block->html();
    }
}
