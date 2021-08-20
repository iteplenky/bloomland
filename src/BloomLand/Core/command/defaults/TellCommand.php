<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class TellCommand extends BaseCommand
{

    /**
     * TellCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('tell', 'Общение друг с другом.', ['msg', 'w', 'whisper']);
        $this->setPermission('core.command.tell');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Чтобы отправить §bличное сообщение§r, используйте: ' .
            '/tell <§bигрок§r> <§7...§bсообщение§r>');
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        if ($target->getId() == $player->getId()) {
            if (empty($args[0])) {
                $player->sendMessage('Временная §bзаметка создана§r. Содержимое §bпустое§r.');
            } else {
                $player->sendMessage('Временная §bзаметка создана§r. Содержимое: §b' . implode(' ', $args));
            }
            return;
        }

        $target->sendMessage('Игрок §b' . $player->getName() . ' §rнаписал Вам §rсообщение: §e' .
            implode(' ', $args) . '§r.');
        $player->sendMessage('Вы отправили игроку §b' . $target->getName() . ' §rсообщение: §e' .
            implode(' ', $args) . '§r.');
    }
}