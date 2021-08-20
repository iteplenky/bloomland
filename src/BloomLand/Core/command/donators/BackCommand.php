<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class BackCommand extends BaseCommand
{

    /**
     * BackCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('back', 'Вернуться на место смерти.');
        $this->setPermission('core.command.back');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (is_null($player->getBackPosition())) {
            $player->sendMessage('Вы еще не умирали.');
            return;
        }

        $player->teleport($player->getBackPosition());
        $player->sendMessage('Вы переместились на место смерти.');
    }
}