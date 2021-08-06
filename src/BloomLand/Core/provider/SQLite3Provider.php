<?php


namespace BloomLand\Core\provider;


use BloomLand\Core\BLPlayer;
use BloomLand\Core\Core;

use BloomLand\Core\utils\Utils;
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
     * @param int $id
     * @return bool
     */
    public function exists(int $id) : bool
    {
        $player = Utils::getPlayer($id);
        $username = $player->getLowerCaseName();
        $result = $this->getDatabase()->query("SELECT * FROM data WHERE username = '$username'");
        return !empty($result->fetchArray(SQLITE3_ASSOC));
    }

    /**
     * @param int $id
     * @return bool
     */
    public function new(int $id) : bool
    {
        $player = Utils::getPlayer($id);
        $statement = $this->getDatabase()->prepare( 'INSERT INTO data (username, coins) VALUES (:username, :coins)');
        $statement->bindValue(':username', $player->getLowerCaseName());
        $statement->bindValue(':coins', BLPlayer::DEFAULT_COINS);
        $statement->execute();
        return $this->getDatabase()->changes() == 1;
    }

    /**
     * @param string $username
     * @return int
     */
    public function getCoins(string $username) : int
    {
        return $this->database->query( 'SELECT coins FROM data WHERE username = \'$username\'' )->fetchArray(SQLITE3_ASSOC)['coins'];
    }

    /**
     * @param string $username
     * @param int $count
     */
    public function setCoins(string $username, int $count) : void
    {
        $this->database->query( 'UPDATE data SET coins = \'$count\' WHERE username = \'$username\'');
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