<?php


namespace BloomLand\Core\inventory\type;


use BloomLand\Core\inventory\InvMenu;
use BloomLand\Core\inventory\type\graphic\InvMenuGraphic;

use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuType
{

    /**
     * @param InvMenu $menu
     * @param Player $player
     * @return InvMenuGraphic|null
     */
    public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic;

    /**
     * @return Inventory
     */
    public function createInventory() : Inventory;
}