<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class FireCommand extends BaseCommand
{

    /**
     * FireCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('fire', 'Поджечь игрока.');
        $this->setPermission('core.command.fire');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Чтобы §bподжечь §rигрока, используйте: /fire <§bигрок§r>');
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        if ($target->isCreative()) {
            $player->sendMessage('Игрок §b' . $target->getName() . ' §rв Творческом режиме.');
            return;
        }

        if ($target->isOnFire()) {
            $player->sendMessage('Игрок §b' . $target->getName() . ' §rи так горит.');
            return;
        }

        $target->setOnFire(15);
        $target->sendMessage('Игрок §b' . $target->getName() . ' §rподжог Вас.');
        $player->sendMessage('Вы подожгли игрока §b' . $target->getName() . '§r.');
    }
}