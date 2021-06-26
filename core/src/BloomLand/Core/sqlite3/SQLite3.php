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

    }
