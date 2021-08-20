<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\math\Vector3;
use pocketmine\player\Player;

class TopCommand extends BaseCommand
{

    /**
     * TopCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('top', 'Переместиться к облакам.');
        $this->setPermission('core.command.top');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $x = $player->getLocation()->getFloorX();
        $z = $player->getLocation()->getFloorZ();

        $player->teleport(new Vector3($x, 256, $z));
        $player->sendMessage('Вы §bпереместились §rк облакам.');
    }
}