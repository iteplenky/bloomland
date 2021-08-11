<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class KillCommand extends BaseCommand
{

    /**
     * KillCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('kill', 'Совершить самоубийство.', ['suicide']);
        $this->setPermission('core.command.kill');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_SUICIDE, 1000));
        $player->sendMessage('Вы совершили самоубийство.');
    }
}