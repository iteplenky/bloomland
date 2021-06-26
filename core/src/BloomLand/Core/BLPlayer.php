<?php


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

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function getLowerCaseName() : string
        {
            return strtolower($this->username);
        }

        public function loadPlayer() : void 
        {
            $this->messages = new MessageEntry($this);
        }

        public function getCriminalRecord() : CriminalRecord
        {
            return $this->criminalRecord;
        }

        public function getDatabase() : SQLite3
        {
            return Core::getDatabase();
        }

        public function getDevice() : string
        {
            return $this->device ?? 'unknown';
        }

        public function getLastDevice() : string
        {
            return $this->lastDevice;
        }

        public function getMoney() : int
        {
            return Economy::getMoney($this->getLowerCaseName());
        }

        public function addMoney(int $count) : void
        {
            Economy::addMoney($this->getLowerCaseName(), $count);
        }

        public function removeMoney(int $count) : void
        {
            Economy::removeMoney($this->getLowerCaseName(), $count);
        }

        public function setMoney(int $count) : void
        {
            Economy::setMoney($this->getLowerCaseName(), $count);
        }

        public function isBanned() : bool
        {
            return Ban::isBanned($this->getLowerCaseName());
        }
    
        public function getLanguage() : string
        {
            return $this->language;
        }
    
        public function setLanguage(string $language, bool $update = true) : void
        {
            $this->language = Language::getLanguage($language);
    
            if ($update) $this->updateDatabase('lang', $this->language);
        }

        public function translate(string $message, array $parameters = []) : string
        {
            return Language::translate($this->getLanguage(), $message, $parameters);
        }
    
        public function translateExtended(string $message, array $args = [], string $separator = '%') : string
        {
            return Language::translateExtended($this->getLanguage(), $message, $args, $separator);
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

        public function getLastAttacker() : ?BLPlayer
        {
            return $this->lastAttacker;
        }

        public function setLastAttacker(?BLPlayer $lastAttacker) : void
        {
            $this->lastAttacker = $lastAttacker;
        }

        public function reset(bool $updateName = true, bool $clearInventory = true, GameMode $gameMode = null) : void
        {
            if ($clearInventory and $this->inventory !== null and $this->armorInventory !== null) {
                $this->inventory->clearAll();
                $this->armorInventory->clearAll();
            }
    
            $this->setGamemode($gameMode ?? GameMode::SURVIVAL());
        
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

	    
        public function isTagged() : bool
        {
            return (time() - $this->combatTag) <= 15 ? true : false;
        }

        public function getCombatTagTime() : int
        {
            return $this->combatTag;
        }
        
    }

?>
