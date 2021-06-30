<?php


namespace iteplenky\RegUI;


    use iteplenky\RegUI\command\RegisterCommand;
    use iteplenky\RegUI\command\LoginCommand;
    use iteplenky\RegUI\command\LogoutCommand;
    use iteplenky\RegUI\command\ChangePasswordCommand;
    use iteplenky\RegUI\command\VkCommand;

    use pocketmine\plugin\PluginBase;
    use pocketmine\player\Player;

    class Main extends PluginBase
    {
        
        /** @var SQLite3 база данных*/
        private $database;
        
        /** @var array конфиг*/
        public $config;
        
        /** @var array список игроков которые авторизованы*/
        private $logined = [];
        
        /** @var array будут сохраняться попытки входа*/
        public $logCount = [];
        
        /**
         * регистрация команд
         * @return void
         */
        public function onLoad() : void
        {
            $commands = [
            new RegisterCommand($this, 'register', 'Зарегистрировать аккаунт', 'register.cmd'),
            new LoginCommand($this, 'login', 'Войти в аккаунт', 'login.cmd'),
            new LogoutCommand($this, 'logout', 'Сбросить аккаунт', 'logout.cmd'),
            new ChangePasswordCommand($this, 'changepassword', 'Сменить пароль', 'changepassword.cmd', ['cp'])
            //   new VkCommand($this, 'vk', 'Поменять id ВКонтакте', 'vk.cmd')
            ];
            
            foreach ($commands as $command)
                $this->getServer()->getCommandMap()->register('RegUI', $command);
        }
        
        /**
         * @return void
         */
        public function onEnable() : void
        {
            $this->saveDefaultConfig();
            $this->config = $this->getConfig()->getAll(); //получения конфига в виде массива
            
            $this->database = new \SQLite3($this->getDataFolder() . 'database.db'); //создание базы данных

            $this->getDatabase()->exec("CREATE TABLE IF NOT EXISTS players(player TEXT PRIMARY KEY COLLATE NOCASE, password TEXT, IpAddress TEXT, vkId TEXT)"); //создание таблицы в базе данных
            
            $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this); //регистрация событий
            $this->getLogger()->notice('Игроков в базе: ' . $this->getPlayersCount());
        }

        public function getDatabase() : \SQLite3
        {
            return $this->database;
        }
        
        /**
         * проверит есть ли игрок в базе данных
         * @param string $player
         * @return bool
         */
        public function isRegistered(string $player) : bool
        {
            $db = $this->getDatabase()->prepare("SELECT player FROM players WHERE player =:player;");

            $db->bindValue(":player", strtolower($player), SQLITE3_TEXT);
            
            $result = $db->execute();
            $result = $result->fetchArray(SQLITE3_ASSOC);
            
            $db->close();
            
            if (empty($result)) return false;
            else return true;
        }
        
        /**
         * проверить авторизован ли игрок
         * @param string $player
         * @return bool
         */
        public function isLogined(string $player) : bool
        {
            return in_array(strtolower($player), $this->logined);
        }
        
        /**
         * true - добавит игрока в массив logined
         * false - удалит игрока из массива logined
         * @param string $player
         * @param bool $value
         * @return void
         */
        public function setLogined(string $player, bool $value = true) : void
        {
            switch($value) {
                
                case true:
                    $this->logined[] = strtolower($player);
                break;

                case false:
                    unset($this->logined[array_search(strtolower($player), $this->logined, true)]);
                break;
            }

        }
        
        /**
         * @param Player $player
         * @param string $password
         * @param string $vkId
         * @return void
         */
        public function register(Player $player, string $password, string $vkId) : void
        {
            $db = $this->getDatabase()->prepare("INSERT OR REPLACE INTO players(player, password, IpAddress, vkId) VALUES(:player, :password, :IpAddress, :vkId)");

            $db->bindValue(":player", strtolower($player->getName()), SQLITE3_TEXT);
            $db->bindValue(":password", $password, SQLITE3_TEXT);
            $db->bindValue(":IpAddress", $player->getNetworkSession()->getIp(), SQLITE3_TEXT);
            $db->bindValue(":vkId", strtolower($vkId), SQLITE3_TEXT);
            
            $db->execute();
            $db->close();
        }
        
        /**
         * @param string $player
         * @return array
         */
        public function getData(string $player) : array
        {
            $db = $this->getDatabase()->prepare("SELECT * FROM players WHERE player=:player;");

            $db->bindValue(":player", strtolower($player));
            
            $end = $db->execute();
            $array = $end->fetchArray(SQLITE3_ASSOC);
            
            $db->close();
            
            return $array;
        }
        
        /**
         * @param string $player
         * @param string $password
         * @return void
         */
        public function changepassword(string $player, string $password) : void
        {
            $db = $this->getDatabase()->prepare("UPDATE players SET password=:newpassword WHERE player=:player;");

            $db->bindValue(":newpassword", $password);
            $db->bindValue(":player", strtolower($player));
            
            $db->execute();
            $db->close();
        }
        
        /**
         * @param string $player
         * @return void
         */
        public function logout(string $player) : void
        {
            $db = $this->getDatabase()->prepare("DELETE FROM players WHERE player=:player;");

            $db->bindValue(":player", strtolower($player));
            
            $db->execute();
            $db->close();
            
            if ($this->isLogined($player)) $this->setLogined($player, false);
            
            if ($this->getServer()->getPlayerByPrefix($player) !== null) 
                $this->getServer()->getPlayerByPrefix($player)->kick($this->config["success"]["logout"]);
            
        }
        
        /**
         * @param string $player
         * @param int $ip
         * @return void
         */
        public function updateIP(string $player, string $ip) : void
        {
            $db = $this->getDatabase()->prepare("UPDATE players SET IpAddress=:IpAddress WHERE player=:player;");

            $db->bindValue(":IpAddress", $ip);
            $db->bindValue(":player", strtolower($player));
            
            $db->execute();
            $db->close();
        }
        
        /**
         * @return int
         */
        public function getPlayersCount() : int
        {
            $rows = $this->getDatabase()->query("SELECT COUNT(*) as count FROM players");
            $count = $rows->fetchArray(SQLITE3_ASSOC);
            return $count['count'];
        }

    }

?>
