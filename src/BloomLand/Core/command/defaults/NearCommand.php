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
        // todo.
    }
}