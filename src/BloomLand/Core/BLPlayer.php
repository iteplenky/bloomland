<?php


namespace BloomLand\Core;


use JetBrains\PhpStorm\Pure;

use pocketmine\math\Vector3;
use pocketmine\player\Player;

use BloomLand\Core\bossbar\BossBar;
use BloomLand\Core\scoreboard\ScoreboardFactory;

class BLPlayer extends Player
{

    public const DEFAULT_COINS = 0;

    /**
     * @var string
     */
    private string $device = '';

    /**
     * @var int
     */
    protected int $lastChatTime = 0;

    /**
     * @var int
     */
    protected int $combatTag = 0;

    /**
     * @var string
     */
    protected string $red_health = 'health';

    /**
     * @var string
     */
    protected string $golden_health = 'golden_health';

    /**
     * @var bool
     */
    private bool $afk = false;

    /**
     * @var bool
     */
    private bool $spy = false;

    /**
     * @var bool
     */
    private bool $god = false;

    /**
     * @var bool
     */
    private bool $offDrop = false;

    /**
     * @var Vector3|null
     */
    private Vector3|null $backPosition = null;

    /**
     * @param string $device
     */
    public function joined(string $device) : void
    {
        $this->setDevice($device);
        ScoreboardFactory::createScoreboard($this);

        $bar = (new BossBar())->setPercentage($this->getPlugin()->get('bossbar.percentage'));
        $bar->setTitle($this->getPlugin()->get('bossbar.title'));
        $bar->addPlayer($this);
    }

    /**
     * @return string
     */
    #[Pure] 
    public function getLowerCaseName() : string
    {
        return strtolower($this->getName());
    }

    /**
     * @return bool
     */
    public function isOp() : bool
    {
        return $this->getServer()->isOp($this->getName());
    }

    /**
     * @param string $device
     */
    public function setDevice(string $device) : void
    {
        $this->device = $device;
    }

    /**
     * @return string
     */
    public function getDevice() : string
    {
        return $this->device;
    }

    /**
     * @param int $time
     */
    public function setLastChatTime(int $time) : void
    {
        $this->lastChatTime = $time;
    }

    /**
     * @return int
     */
    public function getLastChatTime() : int
    {
        return $this->lastChatTime ?? 0;
    }

    /**
     * @return bool
     */
    public function isFighting() : bool
    {
        return (time() - $this->combatTag) <= 15;
    }

    /**
     * @param bool $value
     */
    public function setFighting(bool $value = true) : void
    {
        $value ? $this->combatTag = time() : $this->combatTag = 0;
    }

    /**
     * @return string
     */
    public function getRedHealth() : string
    {
        return $this->red_health;
    }

    /**
     * @return string
     */
    public function getGoldenHealth() : string
    {
        return $this->golden_health;
    }

    /**
     * @return string
     */
    public function getStringHealth() : string
    {
        return $this->getAbsorption() > 0 ?
            round($this->getHealth()) . ' ' . $this->getRedHealth() . ' ' . $this->getAbsorption() . ' ' .
            $this->getGoldenHealth() :
            round($this->getHealth()) . ' ' . $this->getRedHealth();
    }

    /**
     * @return bool
     */
    public function isAfk() : bool
    {
        return $this->afk;
    }

    /**
     * @param bool $afk
     */
    public function setAfk(bool $afk = true) : void
    {
        $this->afk = $afk;
    }

    /**
     * @return bool
     */
    public function isSpy() : bool
    {
        return $this->spy;
    }

    /**
     * @param bool $spy
     */
    public function setSpy(bool $spy = true) : void
    {
        $this->spy = $spy;
    }

    /**
     * @return bool
     */
    public function isGod() : bool
    {
        return $this->god;
    }

    /**
     * @param bool $god
     */
    public function setGod(bool $god = true) : void
    {
        $this->god = $god;
    }

    /**
     * @return bool
     */
    public function isOffDrop() : bool
    {
        return $this->offDrop;
    }

    /**
     * @param bool $offDrop
     */
    public function setOffDrop(bool $offDrop = true) : void
    {
        $this->offDrop = $offDrop;
    }

    /**
     * @param float $fallDistance
     */
    public function fall(float $fallDistance) : void
    {
        if ($this->isGod()) {
            return;
        }
        parent::fall($fallDistance);
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return Core::getInstance();
    }

    /**
     * @return Vector3|null
     */
    public function getBackPosition() : ?Vector3
    {
        return $this->backPosition;
    }

    /**
     * @param Vector3|null $backPosition
     */
    public function setBackPosition(?Vector3 $backPosition) : void
    {
        $this->backPosition = $backPosition;
    }
}