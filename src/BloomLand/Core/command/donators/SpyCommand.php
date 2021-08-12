<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SpyCommand extends BaseCommand
{

    /**
     * SpyCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('spy', 'Слежка за командами.', ['console']);
        $this->setPermission('core.command.spy');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if ($player->isSpy()) {
            $player->setSpy(false);
            $player->sendMessage('Вы больше не следите за командами.');
            return;
        }
        $player->setSpy();
        $player->sendMessage('Вы следите за командами.');
    }
}