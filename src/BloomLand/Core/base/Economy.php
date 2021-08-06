<?php


namespace BloomLand\Core\base;


use BloomLand\Core\Core;

class Economy
{

    /**
     * @param int $id
     * @return int
     */
    public static function getCoins(int $id) : int
    {
        return Core::getInstance()->getProvider()->getCoins($id);
    }

    /**
     * @param int $id
     * @param int $count
     */
    public static function setCoins(int $id, int $count) : void
    {
        if ($count < 0) {
            $count = 0;
        }
        Core::getInstance()->getProvider()->setCoins(self::getCoins($id), $count);
    }

    /**
     * @param int $id
     * @param int $count
     */
    public static function addCoins(int $id, int $count) : void
    {
        self::setCoins($id, self::getCoins($id) + $count);
    }

    /**
     * @param int $id
     * @param int $count
     */
    public static function removeCoins(int $id, int $count) : void
    {
        if ((self::getCoins($id) - $count) < 0) {
            $count = self::getCoins($id);
        }
        self::setCoins($id, self::getCoins($id) - $count);
    }
}