<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use BloomLand\Core\inventory\InvMenu;
use pocketmine\player\Player;

class TrashCommand extends BaseCommand
{

    /**
     * TrashCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('trash', 'Мусорное ведро.');
        $this->setPermission('core.command.trash');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->sendTitle('§l§7открывается§8..', '', 5, 10, 5);
        InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST)->setName('Мусорное ведро')->send($player);
    }
}