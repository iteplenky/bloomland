<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\player\Player;

class CoordsCommand extends BaseCommand
{

    /**
     * CoordsCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('xyz', 'Управление координатами.', ['coords']);
        $this->setPermission('core.command.xyz');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $pk = new GameRulesChangedPacket();
        $pk->gameRules = ['showcoordinates' => new BoolGameRule(true, true)];
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}