<?php


namespace BloomLand\Core\base;


    use BloomLand\Core\Core;

    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;
        
    class Ban
    {
        public static function add(string $intruder, string $player, mixed $args) : void
        {
            Core::getDatabase()->query("INSERT INTO `intruders` (`username`, `sender`, `reason`) VALUES ('$intruder', '$player', '$args')");
        }

        public static function remove(string $intruder) : void
        {
             Core::getDatabase()->query("DELETE FROM `intruders` WHERE `username` = '{$intruder}'");
        }

        public static function get(string $intruder, string $type) : mixed 
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `intruders` WHERE username = :username");
            $prepare->bindValue('username', $intruder);

            $resource = $prepare->execute()->fetchArray(1);

            return $resource[$type];
        }

        public static function isBanned(string $username) : bool
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `intruders` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (!is_bool($resource)) 
                return true;

            return false;
        }

    }

?>
