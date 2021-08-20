<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class DonateCommand extends BaseCommand
{

    /**
     * DonateCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('donate', 'Привилегии сервера.');
        $this->setPermission('core.command.donate');
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