<?php


namespace BloomLand\Core\scoreboard;


    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\scoreboard\SessionFactory;

    use pocketmine\network\mcpe\protocol\SetScorePacket;
    use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
    use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
    
    use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

    class Scoreboard
    {
        public const CRITERIA_NAME = "dummy";
        public const MIN_LINES = 1;
        public const MAX_LINES = 15;
        public const SORT_ASCENDING = 0;
        public const SORT_DESCENDING = 1;
        public const SLOT_LIST = "list";
        public const SLOT_SIDEBAR = "sidebar";
        public const SLOT_BELOW_NAME = "belowname";

        /** @var bool */
        private bool $isSpawned = false;
        /** @var string[] */
        private array $lines = [];
        private $owner;

        /**
         * Scoreboard constructor.
         * @param BLPlayer $owner
         */
        public function __construct(BLPlayer $owner)
        {
            $this->owner = $owner;
        }

        /**
         * @param string $title
         * @param int $slotOrder
         * @param string $displaySlot
         */
        public function spawn(string $title, int $slotOrder = self::SORT_ASCENDING, string $displaySlot = self::SLOT_SIDEBAR) : void
        {
            if ($this->isSpawned) return;

            $pk = new SetDisplayObjectivePacket();
            
            $pk->displaySlot = $displaySlot;
            $pk->objectiveName = $this->owner->getName();
            $pk->displayName = $title;
            $pk->criteriaName = self::CRITERIA_NAME;
            $pk->sortOrder = $slotOrder;

            $this->owner->getNetworkSession()->sendDataPacket($pk);
            
            $this->isSpawned = true;
        }

        public function despawn() : void
        {
            if (!$this->isSpawned) return;

            $this->isSpawned = false;
            
            $pk = new RemoveObjectivePacket();
            
            $pk->objectiveName = $this->owner->getName();
            
            $this->owner->getNetworkSession()->sendDataPacket($pk);
        }

        /**
         * @param array $lines
         */
        public function setLines(array $lines) : void 
        {
            foreach ($lines as $index => $line) {
                $line = str_replace(
                    ['{MONEY}'],
                    [number_format($this->owner->getMoney(), 0, '', '.')],
                    $line
                );
                $this->setScoreLine($index, $line);
            }
        }

        /**
         * @param int $line
         * @param string $message
         */
        public function setScoreLine(int $line, string $message) : void
        {
            if ($this->isSpawned === false) return;

            if ($line < self::MIN_LINES || $line > self::MAX_LINES) return;

            $entry = new ScorePacketEntry();
            
            $entry->objectiveName = $this->owner->getName();
            $entry->type = $entry::TYPE_FAKE_PLAYER;
            $entry->customName = $message; //. str_repeat(" ", $line);
            $entry->score = $line;
            $entry->scoreboardId = $line;
            
            if (isset($this->lines[$line])) {
                $pk = new SetScorePacket();
                $pk->type = $pk::TYPE_REMOVE;
                $pk->entries[] = $entry;
                $this->owner->getNetworkSession()->sendDataPacket($pk);
            }
            
            $pk = new SetScorePacket();
            $pk->type = $pk::TYPE_CHANGE;
            $pk->entries[] = $entry;
            
            $this->owner->getNetworkSession()->sendDataPacket($pk);
            
            $this->lines[$line] = $message;
        }

        public function removeLines() : void
        {
            foreach ($this->lines as $index => $line) $this->removeLine($index);
        }

        /**
         * @param int $line
         */
        public function removeLine(int $line) : void
        {
            $pk = new SetScorePacket();

            $pk->type = SetScorePacket::TYPE_REMOVE;
            
            $entry = new ScorePacketEntry();
            
            $entry->objectiveName = $this->owner->getName();
            $entry->score = $line;
            $entry->scoreboardId = $line;
            $pk->entries[] = $entry;
            
            $this->owner->getNetworkSession()->sendDataPacket($pk);
            
            unset($this->lines[$line]);
        }

        /**
         * @param int $line
         * @return string|null
         */
        public function getLine(int $line) : string
        {
            return $this->lines[$line] ?? null;
        }

        /**
         * @return bool
         */
        public function isSpawned() : bool
        {
            return $this->isSpawned;
        }

        /**
         * @return Player
         */
        public function getOwner() : BLPlayer
        {
            return $this->owner;
        }

        public function __destruct()
        {
            $this->despawn();
            unset($this->owner);
        }

    }

?>
