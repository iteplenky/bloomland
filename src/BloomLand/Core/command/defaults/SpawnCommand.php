<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SpawnCommand extends BaseCommand
{

    /**
     * SpawnCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('spawn', 'Безопасная зона.');
        $this->setPermission('core.command.spawn');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->teleport($player->getWorld()->getSpawnLocation());
        $player->sendMessage($this->getPrefix() . 'Вы §bтелепортировались на спавн§r.');
    }
}