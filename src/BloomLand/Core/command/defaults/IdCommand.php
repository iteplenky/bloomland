<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class IdCommand extends BaseCommand
{

    /**
     * IdCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('id', 'Узнать ID предмета.', ['getid']);
        $this->setPermission('core.command.id');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $itemInHand = $player->getInventory()->getItemInHand();

        if ($itemInHand->getId() == 0) {
            $player->sendMessage('У Вас в руке §bничего §rнет.');
            return;
        }

        $player->sendMessage('У Вас в руке §bпредмет §rс ID: §b' . $itemInHand->getId() . '§r.');
    }
}