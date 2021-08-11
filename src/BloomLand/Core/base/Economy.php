<?php


namespace BloomLand\Core\base;


use BloomLand\Core\Core;

class Economy
{

    /**
     * @param string $username
     * @return int
     */
    public static function getCoins(string $username) : int
    {
        return Core::getInstance()->getProvider()->getCoins($username);
    }

    /**
     * @param string $username
     * @param int $count
     */
    public static function setCoins(string $username, int $count) : void
    {
        if ($count < 0) {
            $count = 0;
        }
        Core::getInstance()->getProvider()->setCoins($username, $count);
    }

    /**
     * @param string $username
     * @param int $count
     */
    public static function addCoins(string $username, int $count) : void
    {
        self::setCoins($username, self::getCoins($username) + $count);
    }

    /**
     * @param string $username
     * @param int $count
     */
    public static function removeCoins(string $username, int $count) : void
    {
        if ((self::getCoins($username) - $count) < 0) {
            $count = self::getCoins($username);
        }
        self::setCoins($username, self::getCoins($username) - $count);
    }
}