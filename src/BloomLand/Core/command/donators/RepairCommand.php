<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\item\Durable;
use pocketmine\player\Player;

class RepairCommand extends BaseCommand
{

    /**
     * RepairCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('repair', 'Починить предмет.');
        $this->setPermission('core.command.repair');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $heldItem = $player->getInventory()->getItemInHand();
        $item = $heldItem->getId();

        if ($item == 0) {
            $player->sendMessage('Чтобы восстановить предмет нужно взять его в руку.');
            return;
        }

        if (!$heldItem instanceof Durable) {
            $player->sendMessage('Вы уверены в том, что этот предмет можно починить?');
            return;
        }

        $heldItem->setDamage(0);

        $player->getInventory()->setItemInHand($heldItem);
        $player->sendMessage('Предмет в руке восстановлен.');
    }
}