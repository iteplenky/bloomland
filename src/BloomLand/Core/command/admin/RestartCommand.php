<?php


namespace BloomLand\Core\command\admin;


use BloomLand\Core\command\BaseCommand;

use BloomLand\Core\task\ReloadingTask;
use BloomLand\Core\utils\Utils;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;

class RestartCommand extends BaseCommand
{

    /**
     * RestartCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('restart', 'Изменить время перезагрузки.', ['reload']);
        $this->setPermission('core.command.restart');
    }

    /**
     * @param Player $player
     * @param array $args
     * @throws CancelTaskException
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Чтобы §bизменить §rвремя перезагрузки, используйте: /restart <§bчисло§r>');
            return;
        }

        $minute = (int) array_shift($args);

        if ($minute > 360 || $minute < 0) {
            $player->sendMessage('§cВы вышли за границы возможного изменения времени.');
        }

        $task = new ReloadingTask();
        $task->setMinutesLeft($minute);
        $task->onRun();

        $player->sendMessage('Вы §bизменили §rвремя на §b' . Utils::convertCase($minute, ['минуту', 'минуты', 'минут'])
            . '§r.');
    }
}