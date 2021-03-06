<?php


namespace BloomLand\Core\command\defaults;


use BloomLand\Core\command\BaseCommand;

use pocketmine\network\mcpe\protocol\ShowProfilePacket;
use pocketmine\player\Player;

class XboxCommand extends BaseCommand
{

    /**
     * XboxCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('xbox', 'Профиль игрока XBOX.');
        $this->setPermission('core.command.xbox');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if (!isset($args[0])) {
            $player->sendMessage('Чтобы §bпросмотреть аккаунт XBOX§r, используйте: /xbox <§bигрок§r>');
            return;
        }

        $target = array_shift($args);

        if (!($target = $this->getPlugin()->getServer()->getPlayerByPrefix($target)) instanceof Player) {
            $player->sendMessage('Игрок сейчас §cне в игре§r.');
            return;
        }

        $pk = new ShowProfilePacket();
        $pk->xuid = $target->getXuid();

        $player->getNetworkSession()->sendDataPacket($pk);
    }
}