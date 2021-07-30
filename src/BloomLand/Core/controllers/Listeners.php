<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\listener\PlayerListener;

use pocketmine\event\Listener;

class Listeners implements Listener
{

    private ?Core $plugin;

    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->loadListeners();
    }

    public function getPlugin() : ?Core
    {
        return $this->plugin;
    }

    public function loadListeners() : void
    {
        new PlayerListener();
    }
}