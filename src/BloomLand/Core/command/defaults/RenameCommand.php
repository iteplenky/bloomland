<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class RenameCommand extends BaseCommand
{

    /**
     * RenameCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('rename', 'Переименовать предмет.');
        $this->setPermission('core.command.rename');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $heldItem = $player->getInventory()->getItemInHand();

        if ($heldItem->getId() == 0) {
            $player->sendMessage('Чтобы переименовать предмет нужно взять его в руку.');
            return;
        }

        if (!isset($args[0])) {
            $player->sendMessage('Чтобы переименовать предмет нужно указать его новое название.');
            return;
        }

        $name = implode(' ', $args);

        if (strlen($name) > 12) {
            $player->sendMessage('Название предмета уж слишком велико.');
            return;
        }

        $heldItem->setCustomName($name);
        $player->getInventory()->setItemInHand($heldItem);

        $player->sendMessage('Вы сменили название предмета на ' . $name . '§r.');
    }
}