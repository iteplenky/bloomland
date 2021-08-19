<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SayCommand extends BaseCommand
{

    /**
     * SayCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('say', 'Оповестить всех игроков.', ['broadcast', 'bc']);
        $this->setPermission('core.command.say');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Чтобы §bоповестить всех§r, используйте: /say <§bтекст§r>');
            return;
        }
        $this->getPlugin()->getServer()->broadcastMessage(' §l§c? §r> Игрок §b' . $player->getName() . ' §rвещает: §6' . implode(' ', $args));
    }
}