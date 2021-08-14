<?php


namespace BloomLand\Core\inventory\type;


use BloomLand\Core\inventory\InvMenu;
use BloomLand\Core\inventory\inventory\InvMenuInventory;

use BloomLand\Core\inventory\type\graphic\BlockActorInvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\InvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\MultiBlockInvMenuGraphic;
use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;

use BloomLand\Core\inventory\type\util\InvMenuTypeHelper;

use pocketmine\block\Block;
use pocketmine\block\tile\Chest;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\world\World;

final class DoublePairableBlockActorFixedInvMenuType implements FixedInvMenuType
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
     * DoublePairableBlockActorFixedInvMenuType constructor.
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

        $graphics = [];
        $menu_name = $menu->getName();
        foreach([
            [$origin, $origin->east()],
            [$origin->east(), $origin]
        ] as [$origin_pos, $pair_pos]){
            $graphics[] = new BlockActorInvMenuGraphic(
                $this->block,
                $origin_pos,
                BlockActorInvMenuGraphic::createTile($this->tile_id, $menu_name)
                    ->setInt(Chest::TAG_PAIRX, $pair_pos->x)
                    ->setInt(Chest::TAG_PAIRZ, $pair_pos->z),
                $this->network_translator
            );
        }

        return count($graphics) > 1 ? new MultiBlockInvMenuGraphic($graphics) : $graphics[0];
    }

    /**
     * @return Inventory
     */
    public function createInventory() : Inventory
    {
        return new InvMenuInventory($this->size);
    }
}
