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
            $player->sendMessage('Чтобы §bпосмотреть §rчужой баланс, используйте: /seemoney <§bигрок§r>');
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        $player->sendMessage('Игровой баланс игрока §b' . $target->getName() . ' §r> §e' .
            Economy::getCoins($target->getLowerCaseName()) . ' §rмонет.');
        $target->sendMessage('Игрок §b' . $player->getName() . ' §rпросмотрел Ваш игровой баланс.');
    }
}