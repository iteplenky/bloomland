<?php


namespace BloomLand\Core\utils;


use pocketmine\player\Player;
use pocketmine\Server;
use InvalidArgumentException;

class Utils
{

    /**
     * @param string $function
     * @return array
     */
    public static function getPlayers(string $function = 'getLowerCaseName') : array
    {
        $players = [];

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $players[$player->{$function}()] = $player;
        }
        return $players;
    }

    /**
     * @param int $id
     * @return Player|null
     */
    public static function getPlayer(int $id) : ?Player
    {
        $player = Server::getInstance()->getWorldManager()->findEntity($id);
        return $player instanceof Player ? $player : null;
    }

    /**
     * @param int $number
     * @param array $after
     * @param string $tag
     * @return string
     */
    public static function convertCase(int $number, array $after, string $tag = '') : string
    {
        $cases = [2, 0, 1, 1, 1, 2];

        if (count($after) < 3) {
            throw new InvalidArgumentException('3 - min');
        }

        return $number . ' ' . $tag . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}