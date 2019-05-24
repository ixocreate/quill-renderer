<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\QuillRenderer;

final class Delta
{
    /**
     * @var array
     */
    private $delta;

    /**
     * @param array $delta
     */
    public function __construct(array $delta)
    {
        $this->delta = $delta;

        if (\array_key_exists('attributes', $this->delta) && $this->delta['attributes'] === null) {
            unset($this->delta['attributes']);
        }

        if (\array_key_exists('insert', $this->delta) && $this->delta['insert'] === null) {
            unset($this->delta['insert']);
        }
    }

    /**
     * @param null $default
     * @return mixed|null
     */
    public function insert($default = null)
    {
        if (!\array_key_exists('insert', $this->delta)) {
            return $default;
        }

        return $this->delta['insert'];
    }

    /**
     * @param null $default
     * @return mixed|null
     */
    public function attributes($default = null)
    {
        if (!\array_key_exists('attributes', $this->delta)) {
            return $default;
        }

        return $this->delta['attributes'];
    }
}
