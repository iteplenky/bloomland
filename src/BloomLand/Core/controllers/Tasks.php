<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\task\BroadcasterTask;
use BloomLand\Core\task\ReloadingTask;

class Tasks
{

    private Core $plugin;

    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->loadTasks();
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }

    public function loadTasks() : void
    {
        new BroadcasterTask();
        new ReloadingTask();
    }
}