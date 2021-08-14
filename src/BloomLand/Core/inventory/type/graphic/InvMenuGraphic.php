<?php


namespace BloomLand\Core\inventory\type\graphic;


use BloomLand\Core\inventory\type\graphic\network\InvMenuGraphicNetworkTranslator;

use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

interface InvMenuGraphic
{

    /**
     * @param Player $player
     * @param string|null $name
     */
    public function send(Player $player, ?string $name) : void;

    /**
     * @param Player $player
     * @param Inventory $inventory
     * @return bool
     */
    public function sendInventory(Player $player, Inventory $inventory) : bool;

    /**
     * @param Player $player
     */
    public function remove(Player $player) : void;

    /**
     * @return InvMenuGraphicNetworkTranslator|null
     */
    public function getNetworkTranslator() : ?InvMenuGraphicNetworkTranslator;
}