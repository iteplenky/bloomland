<?php


namespace BloomLand\Core\command\donators;


use BloomLand\Core\command\BaseCommand;

use pocketmine\player\Player;

class VanishListCommand extends BaseCommand
{

    /**
     * VanishListCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('vlist', 'Список невидимок.', ['vanishlist']);
        $this->setPermission('core.command.vlist');
    }

    /**
     * @param Player $player
     * @param array $args
     */
    public function onExecute(Player $player, array $args) : void
    {
        $players = [];

        foreach ($player->getServer()->getOnlinePlayers() as $onlinePlayers) {
            if ($onlinePlayers->isInvisible()) {
                $players[] = $onlinePlayers;
            }
        }

        if (($nowInvisible = count($players)) < 1) {
            $player->sendMessage('Сейчас §bнет ни одной §rневидимки.');
            return;
        }

        $playerNames = array_map(function(Player $player) {
            return ($player->isOp() ? '§c' : '§r') . $player->getName();
        }, $players);

        $player->sendMessage($this->getPrefix() . 'Список невидимок: §8(§7' . $nowInvisible . '§8)§r: ' .
            implode('§7, §r', $playerNames) . '§r.');
    }
}