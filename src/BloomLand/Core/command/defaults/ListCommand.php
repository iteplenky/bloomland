<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class ListCommand extends BaseCommand
{

    /**
     * ListCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('list', 'Список игроков в сети.');
        $this->setPermission('core.command.list');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $players = $player->getServer()->getOnlinePlayers();
        
        $nowPlaying = count($players);
        $slots = $player->getServer()->getMaxPlayers();

        $playerNames = array_map(function(Player $player) {
            return ($player->isOp() ? '§c' : '§a') . $player->getName();
        }, $players);

        $player->sendMessage($this->getPrefix() . 'Сейчас играет: §b' . $nowPlaying . ' §rиз §b' . $slots . '§r.'
            .  ' Список: ' . implode('§7, §r', $playerNames) . '§r.');
    }
}