<?php


namespace BloomLand\Core\provider;


use BloomLand\Core\Core;

use pocketmine\player\Player;
use SQLite3;

class SQLite3Provider implements ProviderInterface
{

    /**
     * @var SQLite3
     */
    public SQLite3 $database;

    /**
     * SQLite3Provider constructor.
     * @param Core $core
     */
    public function __construct(Core $core)
    {
        $this->database = new SQLite3($core->getDataFolder() . 'users.db');
        $this->getDatabase()->exec(stream_get_contents($core->getResource('users.sql')));
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function exists(Player $player) : bool
    {
        $username = $player->getLowerCaseName();
        $result = $this->getDatabase()->query("SELECT * FROM data WHERE username = '$username'");
        return !empty($result->fetchArray(SQLITE3_ASSOC));
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function new(Player $player) : bool
    {
        $username = $player->getLowerCaseName();
        $this->getDatabase()->query("INSERT INTO data (username) VALUES ('$username')");
        return $this->getDatabase()->changes() == 1;
    }

    /**
     * @return SQLite3
     */
    public function getDatabase() : SQLite3
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'SQLite3';
    }
}