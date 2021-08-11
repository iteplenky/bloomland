<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\base\Economy;
use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SeeMoneyCommand extends BaseCommand
{

    /**
     * SeeMoneyCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('seemoney', 'Просмотреть чужой игровой баланс.');
        $this->setPermission('core.command.coins');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Используйте: /seemoney <игрок>');
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок не в сети.');
            return;
        }

        $player->sendMessage('Игровой баланс игрока ' . $target->getName() . ' > ' . Economy::getCoins($target->getLowerCaseName()) . ' монет.');
        $target->sendMessage('Игрок ' . $player->getName() . ' просмотрел Ваш игровой баланс.');
    }
}