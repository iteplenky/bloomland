<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class VanishCommand extends BaseCommand
{

    /**
     * SpyCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('v', 'Спрятаться от всех игроков.', ['vanish']);
        $this->setPermission('core.command.v');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->setInvisible(!$player->isInvisible());
        $player->sendMessage('Вы §b' . ($player->isInvisible() ? 'включили' : 'выключили') . ' §rрежим невидимости.');
    }
}