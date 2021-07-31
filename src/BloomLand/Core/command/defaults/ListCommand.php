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
        parent::__construct('list', 'Список игроков в сети.', 'list');
        $this->setPermission('core.command.list');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if ($this->getPlugin()->isEnabled()) {

            $nowPlaying = count($player->getServer()->getOnlinePlayers());
            $slots = $player->getServer()->getMaxPlayers();

            $player->sendMessage($this->getPrefix() . 'Сейчас играет: ' . $nowPlaying . ' из ' . $slots . '.');

        }
    }
}