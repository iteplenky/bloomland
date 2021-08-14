<?php


namespace BloomLand\Core\inventory\type\util\builder;


use InvalidStateException;

use BloomLand\Core\inventory\type\DoublePairableBlockActorFixedInvMenuType;
use BloomLand\Core\inventory\type\graphic\network\BlockInvMenuGraphicNetworkTranslator;

final class DoublePairableBlockActorFixedInvMenuTypeBuilder implements InvMenuTypeBuilder
{

	use BlockInvMenuTypeBuilderTrait;
	use FixedInvMenuTypeBuilderTrait;
	use GraphicNetworkTranslatableInvMenuTypeBuilderTrait;

    /**
     * @var string|null
     */
    private ?string $block_actor_id = null;

    /**
     * DoublePairableBlockActorFixedInvMenuTypeBuilder constructor.
     */
    public function __construct()
    {
		$this->addGraphicNetworkTranslator(BlockInvMenuGraphicNetworkTranslator::instance());
	}

    /**
     * @param string $block_actor_id
     * @return $this
     */
    public function setBlockActorId(string $block_actor_id) : self
    {
		$this->block_actor_id = $block_actor_id;
		return $this;
	}

    /**
     * @return string
     */
    private function getBlockActorId() : string
    {
		if($this->block_actor_id === null){
			throw new InvalidStateException("No block actor ID was specified");
		}

		return $this->block_actor_id;
	}

    /**
     * @return DoublePairableBlockActorFixedInvMenuType
     */
    public function build() : DoublePairableBlockActorFixedInvMenuType
    {
		return new DoublePairableBlockActorFixedInvMenuType($this->getBlock(), $this->getSize(), $this->getBlockActorId(), $this->getGraphicNetworkTranslator());
	}
}