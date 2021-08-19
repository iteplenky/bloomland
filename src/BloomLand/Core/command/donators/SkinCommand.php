<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SkinCommand extends BaseCommand
{

    /**
     * SkinCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('skin', 'Скопировать скин игрока.', ['getskin']);
        $this->setPermission('core.command.skin');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Чтобы §bскопировать §rскин, используйте: /skin <§bигрок§r>');
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        if ($target->getId() == $player->getId()) {
            $player->sendMessage('§cВы пытались скопировать собственный скин.');
            return;
        }

        $player->setSkin($target->getSkin());
        $target->sendMessage('Ваш скин §bскопировал §rигрок §b' . $player->getName() . '§r.');
        $player->sendMessage('Вы §bсменили §rсобственный скин на скин игрока §b' . $target->getName() . '§r.');
    }
}