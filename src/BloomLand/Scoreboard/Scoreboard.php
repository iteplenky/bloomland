<?php


namespace BloomLand\Scoreboard;


use BloomLand\Core\base\Economy;

use pocketmine\player\Player;

use pocketmine\network\mcpe\protocol\{
    SetScorePacket,
    RemoveObjectivePacket,
    SetDisplayObjectivePacket,
    types\ScorePacketEntry,
};

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

    /**
     * @var bool
     */
    private bool $isSpawned = false;

    /**
     * @var array
     */
    private array $lines = [];

    /**
     * @var Player
     */
    private Player $owner;

    /**
     * Scoreboard constructor.
     * @param Player $owner
     */
    public function __construct(Player $owner)
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
        if ($this->isSpawned()) {
            return;
        }

        $pk = new SetDisplayObjectivePacket();

        $pk->displaySlot = $displaySlot;
        $pk->objectiveName = $this->getOwner()->getName();
        $pk->displayName = $title;
        $pk->criteriaName = self::CRITERIA_NAME;
        $pk->sortOrder = $slotOrder;

        $this->getOwner()->getNetworkSession()->sendDataPacket($pk);

        $this->isSpawned = true;
    }

    public function despawn() : void
    {
        if (!$this->isSpawned()) {
            return;
        }

        $this->isSpawned = false;

        $pk = new RemoveObjectivePacket();

        $pk->objectiveName = $this->getOwner()->getName();

        $this->getOwner()->getNetworkSession()->sendDataPacket($pk);
    }

    /**
     * @param array $lines
     */
    public function setLines(array $lines) : void
    {
        foreach ($lines as $index => $line) {
            $index++;
            $this->setScoreLine($index, $line);
        }
    }

    /**
     * @param int $line
     * @param string $message
     */
    public function setScoreLine(int $line, string $message) : void
    {
        if ($this->isSpawned()) {
            if ($line < self::MIN_LINES || $line > self::MAX_LINES) {
                return;
            }

            $entry = new ScorePacketEntry();

            $entry->objectiveName = $this->getOwner()->getName();
            $entry->type = $entry::TYPE_FAKE_PLAYER;
            $entry->customName = $message;
            $entry->score = $line;
            $entry->scoreboardId = $line;

            if (isset($this->lines[$line])) {
                $pk = new SetScorePacket();
                $pk->type = $pk::TYPE_REMOVE;
                $pk->entries[] = $entry;
                $this->getOwner()->getNetworkSession()->sendDataPacket($pk);
            }

            $pk = new SetScorePacket();
            $pk->type = $pk::TYPE_CHANGE;
            $pk->entries[] = $entry;

            $this->getOwner()->getNetworkSession()->sendDataPacket($pk);

            $this->lines[$line] = $message;
        }
    }

    public function removeLines() : void
    {
        foreach ($this->lines as $index => $line) {
            $this->removeLine( $index );
        }
    }

    public function removeLine(int $line) : void
    {
        $pk = new SetScorePacket();

        $pk->type = SetScorePacket::TYPE_REMOVE;

        $entry = new ScorePacketEntry();

        $entry->objectiveName = $this->getOwner()->getName();
        $entry->score = $line;
        $entry->scoreboardId = $line;
        $pk->entries[] = $entry;

        $this->getOwner()->getNetworkSession()->sendDataPacket($pk);

        unset($this->lines[$line]);
    }

    /**
     * @param int $line
     * @return string|null
     */
    public function getLine(int $line) : ?string
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
    public function getOwner() : Player
    {
        return $this->owner;
    }

    public function __destruct()
    {
        $this->despawn();
        unset ($this->owner);
    }
}