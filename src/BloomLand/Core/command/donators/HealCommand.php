<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class HealCommand extends BaseCommand
{

    /**
     * HealCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('heal', 'Восстановить параметры.', ['food', 'feed']);
        $this->setPermission('core.command.heal');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());

        $player->sendMessage('Вы восстановили свои параметры.');
    }
}