<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class GodCommand extends BaseCommand
{

    /**
     * GodCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('god', 'Режим бессмертия.');
        $this->setPermission('core.command.god');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if ($player->isGod()) {
            $player->setGod(false);
            $player->sendMessage('Вы больше не бессмертный.');
            return;
        }
        $player->setGod();
        $player->sendMessage('Вы теперь бессмертный.');
    }
}