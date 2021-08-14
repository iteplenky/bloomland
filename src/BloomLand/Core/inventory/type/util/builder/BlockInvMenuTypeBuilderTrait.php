<?php


namespace BloomLand\Core\inventory\type\util\builder;


use InvalidStateException;

use pocketmine\block\Block;

trait BlockInvMenuTypeBuilderTrait
{

    /**
     * @var Block|null
     */
    private ?Block $block = null;

    /**
     * @param Block $block
     * @return $this
     */
    public function setBlock(Block $block) : self
    {
		$this->block = $block;
		return $this;
	}

    /**
     * @return Block
     */
    protected function getBlock() : Block
    {
		if($this->block === null){
			throw new InvalidStateException("No block was provided");
		}

		return $this->block;
	}
}