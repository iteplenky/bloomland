<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\listener\PlayerListener;
use BloomLand\Core\listener\ItemLimitListener;
use BloomLand\Core\listener\CombatListener;

use pocketmine\event\Listener;

class Listeners implements Listener
{

    private Core $plugin;

    /**
     * Listeners constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->loadListeners();
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }

    public function loadListeners() : void
    {
        new PlayerListener();
        new ItemLimitListener();
        new CombatListener();
    }
}