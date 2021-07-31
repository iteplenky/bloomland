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
     * @return string
     */
    public function getName() : string;
}
