<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class KickCommand extends BaseCommand
{

    /**
     * KickCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('kick', 'Выгнать нарушителя из игры.');
        $this->setPermission('core.command.kick');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (empty($args[0])) {
            $player->sendMessage('Чтобы выгнать нарушителя используйте: /kick <"ник игрока"> <причина>');
            return;
        }

        $name = array_shift($args);

        if (!($intruder = $this->getPlugin()->getServer()->getPlayerByPrefix($name)) instanceof Player) {
            $player->sendMessage('Игрок не в сети.');
            return;
        }

        if (empty($args[0])) {
            $player->sendMessage('Герой, Вы выгнали ' . $intruder->getName() .
                ', не указав причину, пожалуйста осведомите его о причине.');
            $args[0] = 'неизвестна';
        }

        $intruder->kick('Вы выгнаны игроком ' . $player->getName() . '.' . PHP_EOL .
            '| Причина: ' . implode(' ', $args) . PHP_EOL . PHP_EOL .
            '| Оставить жалобу: vk.com/bl_pe');

        $this->getPlugin()->getServer()->broadcastMessage('Игрок ' . $player->getName() .
            ' выгнал нарушителя ' . $intruder->getName() . ', по причине: ' . implode(' ', $args));
    }
}