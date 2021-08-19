<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class MilkCommand extends BaseCommand
{

    /**
     * MilkCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('milk', 'Снять все эффекты.');
        $this->setPermission('core.command.milk');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (count($player->getEffects()->all()) == 0) {
            $player->sendMessage('На Вас нет никаких §bналоженных §rэффектов.');
            return;
        }

        $player->getEffects()->clear();
        $player->sendMessage('Вы §bсняли §rс себя все эффекты.');
    }
}