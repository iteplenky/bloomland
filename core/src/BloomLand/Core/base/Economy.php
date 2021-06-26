<?php


namespace BloomLand\Core\base;


    use BloomLand\Core\Core;

    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;
        
    class Economy
    {
        public static function getMoney(string $username) : int
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (!is_bool($resource)) return (int) $resource['coins'];

            return 0;
        }

        public static function set($username, $table, $value) : void 
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (is_bool($resource)) 
                $prepare = Core::getDatabase()->prepare("INSERT INTO `data` (username, coins) VALUES (:username, :coins)");
            
            else 
                $prepare = Core::getDatabase()->prepare("UPDATE `data` SET coins = :coins WHERE username = :username");
            

            $prepare->bindValue('username', $username);
            $prepare->bindValue('coins', $value);
            $prepare->execute();
        }

    }

?>
