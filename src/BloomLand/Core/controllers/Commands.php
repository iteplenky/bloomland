<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\command\defaults\ListCommand;
use BloomLand\Core\command\defaults\CoinsCommand;
use BloomLand\Core\command\defaults\SpawnCommand;
use BloomLand\Core\command\defaults\AfkCommand;
use BloomLand\Core\command\defaults\NearCommand;
use BloomLand\Core\command\defaults\PayCommand;
use BloomLand\Core\command\defaults\SeeMoneyCommand;
use BloomLand\Core\command\defaults\KillCommand;
use BloomLand\Core\command\defaults\RenameCommand;
use BloomLand\Core\command\defaults\CoordsCommand;

use BloomLand\Core\command\donators\ClearInventoryCommand;
use BloomLand\Core\command\donators\FlyCommand;
use BloomLand\Core\command\donators\HealCommand;
use BloomLand\Core\command\donators\SayCommand;
use BloomLand\Core\command\donators\RepairCommand;
use BloomLand\Core\command\donators\SizeCommand;

use pocketmine\event\Listener;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\server\CommandEvent;

use pocketmine\player\Player;

class Commands implements Listener
{

    private Core $plugin;

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
                new CoinsCommand(),
                new SpawnCommand(),
                new AfkCommand(),
                new NearCommand(),
                new PayCommand(),
                new SeeMoneyCommand(),
                new ClearInventoryCommand(),
                new FlyCommand(),
                new HealCommand(),
                new SayCommand(),
                new KillCommand(),
                new RenameCommand(),
                new RepairCommand(),
                new CoordsCommand(),
                new SizeCommand()
            ]
        );
    }

    public function unregisterCommands() : void
    {
        $commands = [
            'list',
            'say',
            'kill'
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

    /**
     * @param CommandEvent $event
     */
    public function handleCommandEvent(CommandEvent $event) : void
    {
        $command = explode(':', $event->getCommand());
        $command = $command[1] ?? $command[0];

        $player = $event->getSender();

        $blockedCommands = $this->getPlugin()->getConfig()->get('blocked-commands', []);

        if (!is_array($blockedCommands)) {
            $this->getPlugin()->getServer()->getLogger()->error('CONFIG: blocked-commands должен быть массивом.');
            return;
        }

        $command = str_replace(' ', '', $command);
        $datum = $blockedCommands[$command] ?? null;

        if ($datum !== null) {
            if (($datum['console'] ?? false) && $player instanceof ConsoleCommandSender) {
                $event->cancel();
            }
            if (($datum['in-game'] ?? false) && $player instanceof Player) {
                $event->cancel();
            }
        }

        if ($event->isCancelled()) {
            $player->sendMessage('Использование команды ограничено для использования.');
        }
    }
}