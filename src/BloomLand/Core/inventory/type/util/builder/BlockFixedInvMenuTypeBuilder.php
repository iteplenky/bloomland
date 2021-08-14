<?php


namespace BloomLand\Core\inventory\type\util\builder;


use BloomLand\Core\inventory\type\BlockFixedInvMenuType;
use BloomLand\Core\inventory\type\graphic\network\BlockInvMenuGraphicNetworkTranslator;

final class BlockFixedInvMenuTypeBuilder implements InvMenuTypeBuilder
{

	use BlockInvMenuTypeBuilderTrait;
	use FixedInvMenuTypeBuilderTrait;
	use GraphicNetworkTranslatableInvMenuTypeBuilderTrait;

    /**
     * BlockFixedInvMenuTypeBuilder constructor.
     */
    public function __construct()
    {
		$this->addGraphicNetworkTranslator(BlockInvMenuGraphicNetworkTranslator::instance());
	}

    /**
     * @return BlockFixedInvMenuType
     */
    public function build() : BlockFixedInvMenuType
    {
		return new BlockFixedInvMenuType($this->getBlock(), $this->getSize(), $this->getGraphicNetworkTranslator());
	}
}