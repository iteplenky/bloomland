<?php


namespace BloomLand\Core\inventory\type\util\builder;


use InvalidStateException;

trait FixedInvMenuTypeBuilderTrait
{

    /**
     * @var int|null
     */
    private ?int $size = null;

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size) : self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    protected function getSize() : int
    {
        if ($this->size === null) {
            throw new InvalidStateException("No size was provided");
        }

        return $this->size;
    }
}
