<?php


namespace BloomLand\Core;


use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class BLPlayer extends Player
{

    /**
     * @var string
     */
    private string $device = '';

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
}