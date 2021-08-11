<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class SizeCommand extends BaseCommand
{

    /**
     * SayCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('size', 'Изменить собственный размер.');
        $this->setPermission('core.command.size');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Использование: /size <small/reset/big>');
            return;
        }
        switch ($args[0]) {
            case 'small':
                $player->setScale(0.8);
                break;

            case 'reset':
                $player->setScale(1.0);
                break;

            case 'big':
                $player->setScale(1.4);
                break;

            default:
                $player->sendMessage('Чтобы изменить свой размер, используйте: /size <small/reset/big>');
                break;
        }
    }
}