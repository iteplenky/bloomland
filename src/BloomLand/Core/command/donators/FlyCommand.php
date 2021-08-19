<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class FlyCommand extends BaseCommand
{

    /**
     * FlyCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('fly', 'Управление режимом полета.');
        $this->setPermission('core.command.fly');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!$player->isCreative()) {
            if ($player->getAllowFlight()) {

                $player->setAllowFlight(false);
                $player->setFlying(false);

                $player->sendMessage('Вы §bне летаете§r.');

            } else {

                $player->setAllowFlight(true);
                $player->setFlying(true);

                $player->sendMessage('Вы §bтеперь летаете§r.');

            }
        } else {
            $player->sendMessage('Вы не можете §bуправлять §rрежимом полета во время §bТворческого режима§r.');
        }
    }
}