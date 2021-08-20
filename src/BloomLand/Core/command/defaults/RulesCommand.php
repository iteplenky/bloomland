<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class RulesCommand extends BaseCommand
{

    /**
     * RulesCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('rules', 'Правила сервера.');
        $this->setPermission('core.command.rules');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
       // todo: send form
    }
}