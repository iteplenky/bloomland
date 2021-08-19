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

        if (!isset($args[0])) {
            $player->sendMessage('Чтобы §bпереименовать предмет§r, используйте: /rename <§bназвание§r>');
            return;
        }

        if ($heldItem->getId() == 0) {
            $player->sendMessage('Чтобы §bпереименовать предмет §rнужно взять его §bв руку§r.');
            return;
        }

        $name = implode(' ', $args);

        if (strlen($name) > 12) {
            $player->sendMessage('Название предмета §bслишком длинное§r.');
            return;
        }

        $heldItem->setCustomName($name);
        $player->getInventory()->setItemInHand($heldItem);

        $player->sendMessage('Вы §bсменили название§r предмета на §b' . $name . '§r.');
    }
}