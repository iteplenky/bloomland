<?php

declare(strict_types=1);


namespace BloomLand\Core\sqlite3;

    
    use BloomLand\Core\Core; 

    class SQLite3 
    {
        public static function updateValue($username, string $key, $value): void 
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (is_bool($resource)) {
                $prepare = Core::getDatabase()->prepare("INSERT INTO `data` (username, $key) VALUES (:username, :$key)");
            } else {
                $prepare = Core::getDatabase()->prepare("UPDATE `data` SET $key = :$key WHERE username = :username");
            }

            $prepare->bindValue("username", $username);
            $prepare->bindValue($key, $value);
            $prepare->execute();
        }

        public static function addIntruder($username, $sender, $reason): void 
        {
            Core::getDatabase()->query("INSERT INTO `intruders` (`username`, `sender`, `reason`) VALUES ('$username', '$sender', '$reason')");
        }

        public static function removeIntruder($username) : void 
        {
            Core::getDatabase()->query("DELETE FROM `intruders` WHERE `username` = '{$username}'");
        }

        public static function getStringValue($username, string $key): string
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (!is_bool($resource)) {
                return (string) $resource[$key];
            }
            return 'ru_RU';
        }

        public static function getIntValue($username, string $key): int
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (!is_bool($resource)) {
                return (int) $resource[$key];
            }
            return 0;
        }

    }