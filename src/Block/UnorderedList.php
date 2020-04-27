<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Block;

final class UnorderedList extends AbstractList
{
    protected $listType = 'bullet';

    protected $htmlTag = 'ul';

    /**
     * @param BlockInterface $block
     * @return bool
     */
    public function accept(BlockInterface $block): bool
    {
        if ($block instanceof UnorderedList) {
            return true;
        }

        return false;
    }
}
