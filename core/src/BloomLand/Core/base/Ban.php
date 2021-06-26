<?php


namespace BloomLand\Core\base;


    use BloomLand\Core\Core;

    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;
        
    class Ban
    {
        public static function add(string $intruder, string $player, mixed $args) : void
        {
            SQLite3::addIntruder($intruder, $player, $args);
        }

        public static function remove(string $intruder) : void
        {
            SQLite3::removeIntruder($intruder);
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

        public static function getSender(string $username) : string
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `intruders` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            return $resource['sender'];
        }

        public static function getReason(string $username) : string
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `intruders` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            return $resource['reason'];
        }

    }

?>