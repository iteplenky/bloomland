<?php


namespace BloomLand\Core\command\defaults;

use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class CoinsCommand extends BaseCommand
{

    /**
     * CoinsCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('coins', 'Игровой баланс.', 'coins');
        $this->setPermission('core.command.coins');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        if ($this->getPlugin()->isEnabled()) {

            $balance = $this->getPlugin()->getProvider()->getCoins($player->getLowerCaseName());

            $player->sendMessage($this->getPrefix() . 'Ваш баланс: ' . $balance . ' монет.');

        }
    }
}