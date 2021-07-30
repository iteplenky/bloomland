<?php


namespace BloomLand\Core\listener;


use BloomLand\Core\Core;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerListener implements Listener
{

    private ?Core $plugin;

    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    private function getPlugin() : ?Core
    {
        return $this->plugin;
    }

    public function handleJoinEvent(PlayerJoinEvent $event) : void
    {
        $event->setJoinMessage('');

        $player = $event->getPlayer();

        $player->sendMessage('Добро пожаловать на сервер!');
    }

    public function handleQuitEvent(PlayerQuitEvent $event) : void
    {
        $event->setQuitMessage(null);
    }
}