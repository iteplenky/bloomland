<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class ExtinguishCommand extends BaseCommand
{

    /**
     * ExtinguishCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('ext', 'Потушить себя.', ['extinguish']);
        $this->setPermission('core.command.ext');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            if ($player->isOnFire()) {
                $player->extinguish();
                $player->sendMessage('§aВы потушили себя.');
            } else {
                $player->sendMessage('§cВы не горите. §rЧтобы §bпотушить §rдругого игрока, используйте: /ext <§bигрок§r>');
            }
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        if ($target->isOnFire()) {
            $target->extinguish();
            $target->sendMessage('Игрок §b' . $player->getName() . ' §rпотушил Вас.');
            $player->sendMessage('Вы потушили игрока §b' . $player->getName() . '§r.');
        } else {
            $player->sendMessage('Игрок §b' . $target->getName() . ' §rне горит.');
        }
    }
}