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

interface BlockInterface
{
    public function add(InsertInterface $insert): BlockInterface;

    public function isResponsible(Delta $delta): bool;

    public function accept(BlockInterface $currentBlock = null): bool;

    public function html(): string;
}
