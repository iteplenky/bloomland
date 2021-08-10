<?php


namespace BloomLand\Core\base;


use BloomLand\Core\Core;

class Economy
{

    public static function getCoins(string $username) : int
    {
        return Core::getInstance()->getProvider()->getCoins($username);
    }

    public static function setCoins(string $username, int $count) : void
    {
        if ($count < 0) {
            $count = 0;
        }
        Core::getInstance()->getProvider()->setCoins($username, $count);
    }

    public static function addCoins(string $username, int $count) : void
    {
        self::setCoins($username, self::getCoins($username) + $count);
    }

    public static function removeCoins(string $username, int $count) : void
    {
        if ((self::getCoins($username) - $count) < 0) {
            $count = self::getCoins($username);
        }
        self::setCoins($username, self::getCoins($username) - $count);
    }
}