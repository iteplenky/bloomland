<?php


namespace BloomLand\Core\utils;


use pocketmine\player\Player;
use pocketmine\Server;

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
}