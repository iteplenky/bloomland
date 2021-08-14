<?php


namespace BloomLand\Core\inventory\type;


use BloomLand\Core\inventory\InvMenu;
use BloomLand\Core\inventory\inventory\InvMenuInventory;

use BloomLand\Core\inventory\type\graphic\BlockInvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\InvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;

use BloomLand\Core\inventory\type\util\InvMenuTypeHelper;

use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\world\World;

final class BlockFixedInvMenuType implements FixedInvMenuType
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
     * @var InvMenuGraphicNetworkTranslator|null
     */
    private ?InvMenuGraphicNetworkTranslator $network_translator;

    /**
     * BlockFixedInvMenuType constructor.
     * @param Block $block
     * @param int $size
     * @param InvMenuGraphicNetworkTranslator|null $network_translator
     */
    public function __construct(Block $block, int $size, ?InvMenuGraphicNetworkTranslator $network_translator = null)
    {
		$this->block = $block;
		$this->size = $size;
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

		return new BlockInvMenuGraphic($this->block, $origin, $this->network_translator);
	}

    /**
     * @return Inventory
     */
    public function createInventory() : Inventory
    {
		return new InvMenuInventory($this->size);
	}
}