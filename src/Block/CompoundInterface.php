<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Block;

use Ixocreate\QuillRenderer\Insert\InsertInterface;

interface CompoundInterface
{
    public function accept(BlockInterface $block): bool;

    /**
     * @param InsertInterface[] $inserts
     * @return BlockInterface
     */
    public function compound(InsertInterface ...$inserts): BlockInterface;
}
