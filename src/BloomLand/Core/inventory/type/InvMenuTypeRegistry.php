<?php


namespace BloomLand\Core\inventory\type;


use BloomLand\Core\inventory\type\util\InvMenuTypeBuilders;

use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;

final class InvMenuTypeRegistry
{

    /**
     * @var array
     */
    private array $types = [];

    /**
     * @var array
     */
    private array $identifiers = [];

    /**
     * InvMenuTypeRegistry constructor.
     */
    public function __construct()
    {
		$this->register(InvMenuTypeIds::TYPE_CHEST, InvMenuTypeBuilders::BLOCK_ACTOR_FIXED()
			->setBlock(VanillaBlocks::CHEST())
			->setSize(27)
			->setBlockActorId("Chest")
		->build());

		$this->register(InvMenuTypeIds::TYPE_DOUBLE_CHEST, InvMenuTypeBuilders::DOUBLE_PAIRABLE_BLOCK_ACTOR_FIXED()
			->setBlock(VanillaBlocks::CHEST())
			->setSize(54)
			->setBlockActorId("Chest")
		->build());

		$this->register(InvMenuTypeIds::TYPE_HOPPER, InvMenuTypeBuilders::BLOCK_ACTOR_FIXED()
			->setBlock(VanillaBlocks::HOPPER())
			->setSize(5)
			->setBlockActorId("Hopper")
			->setNetworkWindowType(WindowTypes::HOPPER)
		->build());
	}

    /**
     * @param string $identifier
     * @param InvMenuType $type
     */
    public function register(string $identifier, InvMenuType $type) : void
    {
		if (isset($this->types[$identifier])){
			unset($this->identifiers[spl_object_id($this->types[$identifier])], $this->types[$identifier]);
		}

		$this->types[$identifier] = $type;
		$this->identifiers[spl_object_id($type)] = $identifier;
	}

    /**
     * @param string $identifier
     * @return bool
     */
    public function exists(string $identifier) : bool
    {
		return isset($this->types[$identifier]);
	}

    /**
     * @param string $identifier
     * @return InvMenuType
     */
    public function get(string $identifier) : InvMenuType
    {
		return $this->types[$identifier];
	}

    /**
     * @param InvMenuType $type
     * @return string
     */
    public function getIdentifier(InvMenuType $type) : string
    {
		return $this->identifiers[spl_object_id($type)];
	}

    /**
     * @param string $identifier
     * @return InvMenuType|null
     */
    public function getOrNull(string $identifier) : ?InvMenuType
    {
		return $this->types[$identifier] ?? null;
	}

    /**
     * @return array
     */
    public function getAll() : array
    {
		return $this->types;
	}
}