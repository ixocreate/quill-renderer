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

abstract class AbstractList implements BlockInterface, CompoundInterface
{
    /**
     * @var string
     */
    protected $listType = '';

    /**
     * @var string
     */
    protected $htmlTag = '';

    /**
     * @var array
     */
    private $inserts = [];

    /**
     * @var int
     */
    private $key = 0;

    /**
     * @var array
     */
    private $intendMap = [];

    /**
     * @var int
     */
    protected static $intend = 0;

    /**
     * @param InsertInterface ...$inserts
     * @return BlockInterface
     */
    public function finish(InsertInterface ...$inserts): BlockInterface
    {
        $block = clone $this;

        if (!\array_key_exists($this->key, $block->inserts)) {
            $block->inserts[$this->key] = [];
            $block->intendMap[$this->key] = static::$intend;
        }

        foreach ($inserts as $insert) {
            $block->inserts[$this->key][] = $insert;
        }

        return $block;
    }

    /**
     * @param Delta $delta
     * @return bool
     */
    public function isResponsible(Delta $delta): bool
    {
        if (
            $delta->insert() === "\n"
            && \is_array($delta->attributes())
            && \array_key_exists('list', $delta->attributes())
            && $delta->attributes()['list'] === $this->listType
        ) {
            if (\array_key_exists('indent', $delta->attributes()) ) {
                static::$intend = (int)$delta->attributes()['indent'];
            } else {
                static::$intend = 0;
            }

            return true;
        }

        return false;
    }

    /**
     * @param InsertInterface[] $inserts
     * @return BlockInterface
     */
    public function compound(InsertInterface ...$inserts): BlockInterface
    {
        /** @var AbstractList $block */
        $block = $this->finish(...$inserts);
        $block->key++;

        return $block;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        $curIndex = 0;
        return $this->htmlRecursive(\array_reverse($this->inserts), $curIndex, \count($this->inserts) - 1);
    }

    private function htmlRecursive(array $insertsGroup, int &$curIndex, int $insertCount)
    {
        $html = '';

        for (; $curIndex <= $insertCount; $curIndex++) {
            $key = $curIndex;

            $html .= '<li>';

            foreach ($insertsGroup[$curIndex] as $insert) {
                /** @var InsertInterface $insert */
                $html .= $insert->html();
            }

            if ($key < $insertCount && $this->intendMap[$key] < $this->intendMap[$key + 1]) {
                ++$curIndex;
                $html .= $this->htmlRecursive($insertsGroup, $curIndex, $insertCount);
            }
            $html .= '</li>';

            if ($key < $insertCount && $this->intendMap[$key] > $this->intendMap[$key + 1]) {
                break;
            }
        }

        return '<' . $this->htmlTag . '>' . $html . '</' . $this->htmlTag . '>';
    }
}
