<?php


namespace BloomLand\Core;


use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class BLPlayer extends Player
{

    const DEFAULT_COINS = 0;

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
     * @return string
     */
    #[Pure] public function getLowerCaseName() : string
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
        if ($value) {
            $this->combatTag = time();
            return;
        }
        $this->combatTag = 0;
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
        if ($this->getAbsorption() > 0) {
            return round($this->getHealth()) . ' ' . $this->getRedHealth() . ' ' .
            $this->getAbsorption() . ' ' . $this->getGoldenHealth();
        } else {
            return round($this->getHealth()) . ' ' . $this->getRedHealth();
        }
    }
}