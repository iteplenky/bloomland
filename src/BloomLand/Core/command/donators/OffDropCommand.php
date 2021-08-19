<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class OffDropCommand extends BaseCommand
{

    /**
     * OffDropCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('drop', 'Поднятие предметов.', ['offdrop']);
        $this->setPermission('core.command.drop');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->setOffDrop(!$player->isOffDrop());
        $player->sendMessage('Вы §b' . ($player->isOffDrop() ? 'не можете' : 'теперь можете') . ' §rподнимать вещи.');
    }
}