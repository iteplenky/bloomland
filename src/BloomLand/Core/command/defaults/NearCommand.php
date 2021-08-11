<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class NearCommand extends BaseCommand
{

    /**
     * NearCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('near', 'Игроки рядом.');
        $this->setPermission('core.command.near');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
//        foreach ($player->getWorld()->getPlayers() as $level_player) {
//            if ($nearest_player === null) {
//                $nearest_player = $level_player;
//                $name = $level_player->getName();
//                $player->sendMessage("§aВаш ближайший игрок: $name");
//            } elseif($player->getLocation()->distance($level_player) < $player->getLocation()->distance($nearest_player)) {
//                $nearest_player = $level_player;
//                $player->sendMessage("§aВаш ближайший игрок: $nearest_player->getName()");
//            }
//        }
    }
}