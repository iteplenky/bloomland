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
            $player->sendMessage('Чтобы §bподелиться§r монетами, используйте: /pay <§bигрок§r> <§bсумма§r>');
            return;
        }

        $target = array_shift($args);
        $amount = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        if (!ctype_digit($amount)) {
            $player->sendMessage('Вы указали: §b' . $amount . '§r, но это §bне похоже §rна целое число.');
            return;
        }

        if ($amount < 1 || $amount > 10000000) {
            $player->sendMessage('§rВаша сумма перевода не в рамках §bустановленных границ§r. Интервал от §b1 §rдо §b1§r.§b000§r.§b000 §rмонет.');
            return;
        }

        if ($amount > Economy::getCoins($player->getLowerCaseName())) {
            $player->sendMessage('§cУ Вас нет столько монет чтобы поделиться.');
            return;
        }

        Economy::removeCoins($player->getLowerCaseName(), $amount);
        Economy::addCoins($target->getLowerCaseName(), $amount);

        $player->sendMessage('Вы перевели §b' . Utils::convertCase($amount, ['монету', 'монеты', 'монет']) .
            ' §rигроку §b' . $target->getName() . '§r.');
        $target->sendMessage('Игрок §b' . $player->getName() . ' §rперевел §rВам §b' . Utils::convertCase($amount,
                ['монету', 'монеты', 'монет']) . '§r.');

    }
}