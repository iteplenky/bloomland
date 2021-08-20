<?php


namespace BloomLand\Core\provider;


use BloomLand\Core\Core;
use pocketmine\player\Player;

interface ProviderInterface
{

    /**
     * ProviderInterface constructor.
     * @param Core $core
     */
    public function __construct(Core $core);

    /**
     * @param Player $player
     * @return bool
     */
    public function exists(Player $player) : bool;

    /**
     * @param Player $player
     * @return bool
     */
    public function new(Player $player) : bool;

    /**
     * @param string $username
     * @return int
     */
    public function getCoins(string $username) : int;

    /**
     * @param string $username
     * @param int $count
     */
    public function setCoins(string $username, int $count) : void;

    /**
     * @param string $username
     * @return bool
     */
    public function isScoreboard(string $username) : bool;

    /**
     * @param string $username
     * @param bool $value
     */
    public function setScoreboard(string $username, bool $value = true) : void;

    /**
     * @return string
     */
    public function getName() : string;
}
