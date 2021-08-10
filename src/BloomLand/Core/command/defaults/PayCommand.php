<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\base\Economy;
use BloomLand\Core\command\BaseCommand;

use BloomLand\Core\utils\Utils;
use pocketmine\player\Player;

class PayCommand extends BaseCommand
{

    /**
     * PayCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('pay', 'Поделиться монетами.');
        $this->setPermission('core.command.coins');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[1])) {
            $player->sendMessage('Используйте: /pay <игрок> <сумма>');
            return;
        }

        $target = array_shift($args);
        $amount = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок не в сети.');
            return;
        }

        if (!ctype_digit($amount)) {
            $player->sendMessage('Вы указали: ' . $amount . ', но это не похоже на целое число.');
            return;
        }

        if ($amount < 1 || $amount > 10000000) {
            $player->sendMessage('Ваша сумма перевода не в рамках установленных границ. Интервал от 1 до 1.000.000 монет.');
            return;
        }

        if ($amount > Economy::getCoins($player->getLowerCaseName())) {
            $player->sendMessage('У Вас нет столько монет чтобы поделиться.');
            return;
        }

        Economy::removeCoins($player->getLowerCaseName(), $amount);
        Economy::addCoins($target->getLowerCaseName(), $amount);

        $player->sendMessage('Вы перевели ' . Utils::convertCase($amount, ['монету', 'монеты', 'монет']) . ' игроку ' . $target->getName() . '.');
        $target->sendMessage('Игрок ' . $player->getName() . ' перевел ' . Utils::convertCase($amount, ['монету', 'монеты', 'монет']));
    }
}