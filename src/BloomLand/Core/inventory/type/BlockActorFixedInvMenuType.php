<?php


namespace BloomLand\Core\inventory\type;


use BloomLand\Core\inventory\InvMenu;
use BloomLand\Core\inventory\inventory\InvMenuInventory;

use BloomLand\Core\inventory\type\graphic\BlockActorInvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\InvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;

use BloomLand\Core\inventory\type\util\InvMenuTypeHelper;

use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\world\World;

final class BlockActorFixedInvMenuType implements FixedInvMenuType
{

    /**
     * @var Block
     */
    private Block $block;

    /**
     * @var int
     */
    private int $size;

    /**
     * @var string
     */
    private string $tile_id;

    /**
     * @var InvMenuGraphicNetworkTranslator|null
     */
    private ?InvMenuGraphicNetworkTranslator $network_translator;

    /**
     * BlockActorFixedInvMenuType constructor.
     * @param Block $block
     * @param int $size
     * @param string $tile_id
     * @param InvMenuGraphicNetworkTranslator|null $network_translator
     */
    public function __construct(Block $block, int $size, string $tile_id, ?InvMenuGraphicNetworkTranslator $network_translator = null)
    {
		$this->block = $block;
		$this->size = $size;
		$this->tile_id = $tile_id;
		$this->network_translator = $network_translator;
	}

    /**
     * @return int
     */
    public function getSize() : int
    {
		return $this->size;
	}

    /**
     * @param InvMenu $menu
     * @param Player $player
     * @return InvMenuGraphic|null
     */
    public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic
    {
		$origin = $player->getPosition()->addVector(InvMenuTypeHelper::getBehindPositionOffset($player))->floor();
		if($origin->y < World::Y_MIN || $origin->y >= World::Y_MAX){
			return null;
		}

		return new BlockActorInvMenuGraphic($this->block, $origin, BlockActorInvMenuGraphic::createTile($this->tile_id, $menu->getName()), $this->network_translator);
	}

    /**
     * @return Inventory
     */
    public function createInventory() : Inventory
    {
		return new InvMenuInventory($this->size);
	}
}