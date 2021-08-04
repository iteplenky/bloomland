<?php


namespace BloomLand\Core\task;


use BloomLand\Core\Core;

use pocketmine\scheduler\Task;

class BroadcasterTask extends Task
{

    private Core $plugin;

    /**
     * @var int
     */
    private int $index = 0;

    /**
     * BroadcasterTask constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getScheduler()->scheduleDelayedRepeatingTask($this, 20 * 60, 20 * 60 * 3);
    }

    public function onRun() : void
    {
        $messages = $this->getPlugin()->get('messages');
        back:
        if ($this->index < count($messages)) {
            $this->getPlugin()->getServer()->broadcastMessage($messages[$this->index]);
            $this->index++;
        } else {
            $this->index = 0;
            goto back;
        }
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }
}