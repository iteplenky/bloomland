<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\base\Economy;
use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class CoinsCommand extends BaseCommand
{

    /**
     * CoinsCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('coins', 'Игровой баланс.', ['balance', 'money', 'mymoney']);
        $this->setPermission('core.command.coins');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->sendMessage('Ваш баланс: §e' . Economy::getCoins($player->getLowerCaseName()) . ' §rмонет.');
    }
}