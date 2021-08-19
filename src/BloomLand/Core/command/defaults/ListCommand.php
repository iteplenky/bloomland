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
            return ($player->isOp() ? '§b' : '§a') . $player->getName();
        }, $players);

        $player->sendMessage($this->getPrefix() . 'Сейчас играет: ' . $nowPlaying . ' из ' . $slots . '.'
            .  ' Список: ' . implode('§r' . '§7, §r', $playerNames) . '§r.');
    }
}