<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\command\defaults\ListCommand;
use BloomLand\Core\command\defaults\CoinsCommand;

use pocketmine\event\Listener;

class Commands implements Listener
{

    private ?Core $plugin;

    /**
     * Commands constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->unregisterCommands();
        $this->registerCommands();
    }

    /**
     * @return Core
     */
    public function getPlugin() : Core
    {
        return $this->plugin;
    }

    public function registerCommands() : void
    {
        $map = $this->getPlugin()->getServer()->getCommandMap();

        $map->registerAll($this->getPlugin()->getName(),
            [
                new ListCommand(),
                new CoinsCommand()
            ]
        );
    }

    public function unregisterCommands() : void
    {
        $commands = [
            'list'
        ];

        $map = $this->getPlugin()->getServer()->getCommandMap();

        foreach ($commands as $cmd) {

            $command = $map->getCommand($cmd);

            if ($command !== null) {

                $command->setLabel('old_' . $cmd);
                $map->unregister($command);

            }

        }

    }

}