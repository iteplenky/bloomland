<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SpeedCommand extends BaseCommand
{

    /**
     * SpeedCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('speed', 'Изменить скорость.');
        $this->setPermission('core.command.speed');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
           $player->sendMessage('Чтобы §bсменить скорость§r, используйте: §b/speed §r<§b0§r-§b10§r>.' . PHP_EOL .
               ' §7* §b0 §7- §rСтандартная скорость.');
           return;
        }

        $speed = (int) array_shift($args);

        if ($speed > 10 || $speed < 0) {
            $player->sendMessage('§cВы вышли за границы возможного изменения скорости.');
            return;
        }

        $player->setMovementSpeed($speed + 0.1);
        $player->sendMessage('Вы §bизменили §rсвою скорость на: §b' . $speed . '§r.');
    }
}