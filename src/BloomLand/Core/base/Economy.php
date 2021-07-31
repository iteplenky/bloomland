<?php


namespace BloomLand\Core\base;


use BloomLand\Core\Core;

use pocketmine\player\Player;

class Economy
{

    /**
     * @param Player $player
     * @return int
     */
    public static function getCoins(Player $player) : int
    {
        return Core::getInstance()->getProvider()->getCoins($player->getLowerCaseName());
    }

    /**
     * @param Player $player
     * @param int $count
     */
    public static function setCoins(Player $player, int $count) : void
    {
        if ($count < 0) {
            $count = 0;
        }
        Core::getInstance()->getProvider()->setCoins(self::getCoins($player), $count);
    }

    /**
     * @param Player $player
     * @param int $count
     */
    public static function addCoins(Player $player, int $count) : void
    {
        self::setCoins($player, self::getCoins($player) + $count);
    }

    /**
     * @param Player $player
     * @param int $count
     */
    public static function removeCoins(Player $player, int $count) : void
    {
        if ((self::getCoins($player) - $count) < 0) {
            $count = self::getCoins($player);
        }
        self::setCoins($player, self::getCoins($player) - $count);
    }
}