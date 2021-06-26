<?php


namespace BloomLand\Core\base;


    use BloomLand\Core\Core;

    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\sqlite3\SQLite3;
        
    class Economy
    {
        /**
         * @param string $name
         * @return bool
         */
        public static function exists(string $name) : bool
        {
            $my = MySQL::getData();

            $result = $my->query("SELECT * FROM economy WHERE name = '" . strtolower($name) . "'");
            $my->close();
            return $result->num_rows > 0 ? true : false;
        }

        /**
         * @param BLPlayer $player
         * @return bool
         */
        public static function setDefaultData(BLPlayer $player) : bool
        {
            $name = strtolower($player->getName());
            $my = MySQL::getData();

            if (!self::exists($name)) {

                MySQL::sendDB("INSERT INTO economy (name , money, box_keys, credits) VALUES ('" . $name . "', 0, 5, 20)");
                $my->close();
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @return array
         */
        public static function get(string $name) : array
        {
            $my = MySQL::getData();

            $economy = mysqli_fetch_row($my->query("SELECT * FROM economy WHERE name = '"  . strtolower($name) . "'"));
            $my->close();

            return is_null($economy) ? [strtolower($name), 0, 5, 20] : $economy;

            /*
            * 1 = money
            * 2 = box_keys
            * 3 = credits
            */

        }

        public static function getMoney(string $username): int
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (!is_bool($resource)) 
                return (int) $resource['coins'];

            return 0;
        }
        

        public static function addMoney(string $username, int $amount)
        {
            $value = self::getMoney($username) + $amount;

            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (is_bool($resource)) {
                $prepare = Core::getDatabase()->prepare("INSERT INTO `data` (username, coins) VALUES (:username, :coins)");
            } else {
                $prepare = Core::getDatabase()->prepare("UPDATE `data` SET coins = :coins WHERE username = :username");
            }

            $prepare->bindValue("username", $username);
            $prepare->bindValue("coins", $value);
            $prepare->execute();

        }

        public static function removeMoney(string $username, int $amount)
        {
            $value = self::getMoney($username) - $amount;

            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);

            $resource = $prepare->execute()->fetchArray(1);

            if (is_bool($resource)) {
                $prepare = Core::getDatabase()->prepare("INSERT INTO `data` (username, coins) VALUES (:username, :coins)");
            } else {
                $prepare = Core::getDatabase()->prepare("UPDATE `data` SET coins = :coins WHERE username = :username");
            }

            $prepare->bindValue("username", $username);
            $prepare->bindValue("coins", $value);
            $prepare->execute();
        }

        public static function setMoney(string $username, int $amount)
        {
            $prepare = Core::getDatabase()->prepare("SELECT * FROM `data` WHERE username = :username");
            $prepare->bindValue('username', $username);
    
            $resource = $prepare->execute()->fetchArray(1);
    
            if (is_bool($resource)) 
                $prepare = Core::getDatabase()->prepare("INSERT INTO `data` (username, coins) VALUES (:username, :coins)");
            
            else 
                $prepare = Core::getDatabase()->prepare("UPDATE `data` SET coins = :coins WHERE username = :username");

    
            $prepare->bindValue("username", $username);
            $prepare->bindValue("coins", $amount);
            $prepare->execute();

        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        // public static function setMoney(string $name, int $amount) : bool
        // {
        //     $my = MySQL::getData();

        //     if(self::exists($name)) {

        //         MySQL::sendDB("UPDATE economy SET  money = '" . $amount. "' WHERE name = '" . strtolower($name) . "'");
        //         return true;

        //     }

        //     $my->close();
        //     return false;
        // }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        // public static function addMoney(string $name, int $amount) : bool
        // {
        //     $my = MySQL::getData();

        //     if(self::exists($name)) {

        //         $money = self::getMoney($name) + $amount;
        //         MySQL::sendDB("UPDATE economy SET  money = '" . $money . "' WHERE name = '" . strtolower($name) . "'");
        //         return true;

        //     }

        //     $my->close();
        //     return false;
        // }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function reduceMoney(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                $money = self::getMoney($name) - $amount;
                MySQL::sendDB("UPDATE economy SET  money = '" . $money . "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @return int
         */
        public static function getKeys(string $name) : int
        {
            $name = strtolower($name);
            $data = self::get($name);
            return $data[2];
        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function setKeys(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                MySQL::sendDB("UPDATE economy SET  box_keys = '" . $amount. "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function addKeys(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                $keys = self::getKeys($name) + $amount;
                MySQL::sendDB("UPDATE economy SET  box_keys = '" . $keys . "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function reduceKeys(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                $keys = self::getKeys($name) - $amount;
                MySQL::sendDB("UPDATE economy SET  box_keys = '" . $keys . "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @return int
         */
        public static function getCredits(string $name) : int
        {
            $name = strtolower($name);
            $data = self::get($name);
            return $data[3];
        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function setCredits(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                MySQL::sendDB("UPDATE economy SET  credits = '" . $amount. "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function addCredits(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                $credits = self::getCredits($name) + $amount;
                MySQL::sendDB("UPDATE economy SET  credits = '" . $credits . "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

        /**
         * @param string $name
         * @param int $amount
         * @return bool
         */
        public static function reduceCredits(string $name, int $amount) : bool
        {
            $my = MySQL::getData();

            if(self::exists($name)) {

                $credits = self::getCredits($name) - $amount;
                MySQL::sendDB("UPDATE economy SET  credits = '" . $credits . "' WHERE name = '" . strtolower($name) . "'");
                return true;

            }

            $my->close();
            return false;
        }

    }