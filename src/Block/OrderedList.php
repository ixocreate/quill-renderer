<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer\Block;

final class OrderedList extends AbstractList
{
    protected $listType = 'ordered';

    protected $htmlTag = 'ol';

    /**
     * @param BlockInterface $block
     * @return bool
     */
    public function accept(BlockInterface $block): bool
    {
        if ($block instanceof OrderedList) {
            return true;
        }

        return false;
    }
}
