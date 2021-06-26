<?php

declare(strict_types=1);


namespace BloomLand\Core;

    use BloomLand\Core\Core;

    use BloomLand\Core\lang\Language;
    use BloomLand\Core\sqlite3\SQLite3;

    use BloomLand\Core\utils\Scoreboard;

    use BloomLand\Core\base\Economy;
    use BloomLand\Core\base\Ban;

    use pocketmine\player\Player;
    use pocketmine\player\GameMode;
    use pocketmine\world\Position;

    class BLPlayer extends Player 
    {
        public const SCOREBOARD_MONEY = 5;
        public const SCOREBOARD_COINS = 8;
        public const SCOREBOARD_RANK = 11;
        public const SCOREBOARD_TITLE_PREFIX = ' §r> ';
        public const SCOREBOARD_SUBTITLE_PREFIX = ' §r> ';

        public const MONEY_LIMIT = 5000000;

        public const MODE_PLAYER = 0;
	    public const MODE_TUTORIAL = 1;

        /** @var int */
	    private $mode = self::MODE_PLAYER;

        /** @var bool */
	    public $firstLogin = false;

        /** @var int */
	    private $kills, $deaths;

        /** @var Scoreboard */
	    public $scoreboard;

        /** @var int */
        public $timePlayed, $joinTime;

        /** @var string */
        protected $lastDevice;

        /** @var string */
        public $device = '';

        /** @var int */
	    protected $lastChatTime = 0;

        private $combatTag = 0;

        private $transactionCount = 0;

        protected $language = Language::DEFAULT_LANGUAGE;
        
        /** @var MessageEntry */
	    public $messages;

        /** @var CriminalRecord */
        protected $criminalRecord;

        protected $lastAttacker = null;

        public function getCore() : Core
        {
            return Core::getAPI();
        }

        public function getLowerCaseName() : string
        {
            return strtolower($this->username);
        }

        public function loadBLPlayer() : void 
        {
            $db = Core::getDatabase();
            $this->messages = new MessageEntry($this);
            $this->loadScoreboard();

            // $nickname = $this->getLowerCaseName();
            // $data = Core::getDatabase()->query("SELECT *  FROM `intruders` WHERE `username` = '$nickname'")->fetchArray(SQLITE3_ASSOC);
            // if ($result['username']) {
            //     $this->criminalRecord = new CriminalRecord($this, $data[2], $data[1]);
            // }

        }

        public function getCriminalRecord() : CriminalRecord
        {
            return $this->criminalRecord;
        }

        private function loadScoreboard() : void{
            $this->scoreboard = new Scoreboard($this, '§bStorm§fGames');
            $sbOptions = [
                '§e@scoreboard.you' => $this->getName(),
                '§a@scoreboard.money' => SQLite3::getIntValue($this->getLowerCaseName(), 'coins'),
                '§6@scoreboard.coins' => SQLite3::getIntValue($this->getLowerCaseName(), 'coins')
            ];
            $i = 0;
            foreach($sbOptions as $text => $value){
                if($i !== 0){
                    $this->scoreboard->setLine(++$i, str_repeat('  ', $i + 1));
                }
                $this->scoreboard->setLine(++$i, self::SCOREBOARD_TITLE_PREFIX . $text);
                $this->scoreboard->setLine(++$i, self::SCOREBOARD_SUBTITLE_PREFIX . $value);
            }
        }

        public function getDatabase() : SQLite3
        {
            return Core::getDatabase();
        }
    
        public function getLastDevice() : string
        {
            return $this->lastDevice;
        }

        public function getDevice() : string
        {
            return $this->device ?? "unknown";
        }

        public function getMoney() : int
        {
            return Economy::getMoney($this->getLowerCaseName());
        }

        public function addMoney(int $count) : void
        {
            $this->transactionCount += 1;
            Economy::addMoney($this->getLowerCaseName(), $count);

            Core::getAPI()->getLogger()->notice('Транзакция #' . $this->transactionCount . ' проведена на сумму ' . $count . ' монет. (' . $this->getMoney() . ')');
        }

        public function removeMoney(int $count) : void
        {
            $this->transactionCount += 1;
            Economy::removeMoney($this->getLowerCaseName(), $count);

            Core::getAPI()->getLogger()->notice('Транзакция #' . $this->transactionCount . ' проведена на сумму ' . $count . ' монет. (' . $this->getMoney() . ')');
        }

        public function setMoney(int $count) : void
        {
            $this->transactionCount += 1;
            Economy::setMoney($this->getLowerCaseName(), $count);

            Core::getAPI()->getLogger()->notice('Транзакция #' . $this->transactionCount . ' проведена на сумму ' . $count . ' монет. (' . $this->getMoney() . ')');
        }

        public function isBanned() : bool
        {
            return Ban::isBanned($this->getLowerCaseName());
        }
    
        public function getLang() : string
        {
            return $this->language;
        }
    
        public function setLanguage(string $language, bool $update = true) : void
        {
            $this->language = Language::getLang($language);
    
            if ($update)
                $this->updateDatabase('lang', $this->language);
            
        }

        public function updateDatabase(string $key, $value) : void 
        {
            SQLite3::updateValue($this->getLowerCaseName(), $key, $value);
        }

        public function translate(string $message, array $parameters = []) : string
        {
            return Language::translate($this->getLang(), $message, $parameters);
        }
    
        public function translateExtended(string $message, array $args = [], string $separator = '%') : string
        {
            return Language::translateExtended($this->getLang(), $message, $args, $separator);
        }
    
        public function getTimePlayedNow() : int
        {
            return $this->timePlayed + (time() - $this->joinTime);
        }
    
        public function addTimePlayed(int $add) : void
        {
            $this->timePlayed += $add;
        }
        
        public function getLastChatTime() : int
        {
            return $this->lastChatTime ?? 0;
        }
    
        public function setLastChatTime(int $lastChatTime) : void
        {
            $this->lastChatTime = $lastChatTime;
        }

        public function setLastAttacker(?BLPlayer $lastAttacker) : void
        {
            $this->lastAttacker = $lastAttacker;
        }

        public function getLastAttacker() : ?BLPlayer
        {
            return $this->lastAttacker;
        }

        public function reset(bool $updateName = true, bool $clearInventory = true, GameMode $gameMode = null) : void
        {
            if ($clearInventory and $this->inventory !== null and $this->armorInventory !== null) {
                $this->inventory->clearAll();
                $this->armorInventory->clearAll();
            }
    
            $this->setGamemode($gameMode ?? GameMode::ADVENTURE());
        
            $this->setMaxHealth(20);
            $this->setHealth(20);
        }

        public function getMode() : int
        {
            return $this->mode;
        }

        public function combatTag(bool $value = true) : void
        {
            if ($value) {
                $this->combatTag = time();
                return;
            }
            $this->combatTag = 0;
        }

        /**
         * @return bool
         */
        public function isTagged() : bool
        {
            return (time() - $this->combatTag) <= 15 ? true : false;
        }

        /**
         * @return int
         */
        public function getCombatTagTime() : int
        {
            return $this->combatTag;
        }
        public function onKill() : void
        {
            // todo
        }
    }