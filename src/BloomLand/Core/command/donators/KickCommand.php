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
            $player->sendMessage(
                'Чтобы §bвыгнать нарушителя, §rиспользуйте: /kick §r<§7"§bник игрока§7"§r> §r<§eпричина§r>'
            );
            return;
        }

        $name = array_shift($args);

        if (!($intruder = $this->getPlugin()->getServer()->getPlayerByPrefix($name)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        if (empty($args[0])) {
            $player->sendMessage('§eГерой§r, Вы выгнали §b' . $intruder->getName() .
                '§r, не указав причину, пожалуйста осведомите его о причине.');
            $args[0] = 'неизвестна';
        }

        $intruder->kick(
            '§l| §rВы выгнаны игроком §b' . $player->getName() . '§r.' . PHP_EOL .
            '§l| §rПричина: §e' . implode(" ", $args) . PHP_EOL . PHP_EOL .
            '§l| §rОставить жалобу: §bvk.com/bl_pe'
        );

        $this->getPlugin()->getServer()->broadcastMessage('Игрок §b' . $player->getName() .
            ' §rвыгнал нарушителя §d' . $intruder->getName() . '§r, по причине: §e' . implode(" ", $args));
    }
}