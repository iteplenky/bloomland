<?php


namespace BloomLand\Core\inventory\type\graphic;


use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;

use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\Tile;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\player\Player;

final class BlockActorInvMenuGraphic implements PositionedInvMenuGraphic
{

    /**
     * @param string $tile_id
     * @param string|null $name
     * @return CompoundTag
     */
    public static function createTile(string $tile_id, ?string $name) : CompoundTag
    {
		$tag = CompoundTag::create()->setString(Tile::TAG_ID, $tile_id);
		if($name !== null){
			$tag->setString(Nameable::TAG_CUSTOM_NAME, $name);
		}
		return $tag;
	}

    /**
     * @var BlockInvMenuGraphic
     */
    private BlockInvMenuGraphic $block_graphic;

    /**
     * @var Vector3
     */
    private Vector3 $position;

    /**
     * @var CompoundTag
     */
    private CompoundTag $tile;

    /**
     * @var InvMenuGraphicNetworkTranslator|null
     */
    private ?InvMenuGraphicNetworkTranslator $network_translator;

    /**
     * BlockActorInvMenuGraphic constructor.
     * @param Block $block
     * @param Vector3 $position
     * @param CompoundTag $tile
     * @param InvMenuGraphicNetworkTranslator|null $network_translator
     */
    #[Pure]
    public function __construct(Block $block, Vector3 $position, CompoundTag $tile, ?InvMenuGraphicNetworkTranslator $network_translator = null)
    {
		$this->block_graphic = new BlockInvMenuGraphic($block, $position);
		$this->position = $position;
		$this->tile = $tile;
		$this->network_translator = $network_translator;
	}

    /**
     * @return Vector3
     */
    public function getPosition() : Vector3
    {
		return $this->position;
	}

    /**
     * @param Player $player
     * @param string|null $name
     */
    public function send(Player $player, ?string $name) : void
    {
		$this->block_graphic->send($player, $name);
		if($name !== null){
			$this->tile->setString(Nameable::TAG_CUSTOM_NAME, $name);
		}
		$player->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create($this->position->x, $this->position->y, $this->position->z, new CacheableNbt($this->tile)));
	}

    /**
     * @param Player $player
     * @param Inventory $inventory
     * @return bool
     */
    public function sendInventory(Player $player, Inventory $inventory) : bool
    {
		return $player->setCurrentWindow($inventory);
	}

    /**
     * @param Player $player
     */
    public function remove(Player $player) : void
    {
		$this->block_graphic->remove($player);
	}

    /**
     * @return InvMenuGraphicNetworkTranslator|null
     */
    public function getNetworkTranslator() : ?InvMenuGraphicNetworkTranslator
    {
		return $this->network_translator;
	}
}