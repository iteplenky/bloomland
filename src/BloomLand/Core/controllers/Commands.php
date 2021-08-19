<?php


namespace BloomLand\Core\controllers;


use BloomLand\Core\Core;

use BloomLand\Core\command\defaults\{
    KitCommand,
    ListCommand,
    CoinsCommand,
    SpawnCommand,
    AfkCommand,
    NearCommand,
    PayCommand,
    SeeMoneyCommand,
    KillCommand,
    RenameCommand,
    CoordsCommand,
    TrashCommand,
    XboxCommand
};

use BloomLand\Core\command\donators\{
    ClearInventoryCommand,
    FlyCommand,
    GodCommand,
    HealCommand,
    KickCommand,
    SayCommand,
    RepairCommand,
    SizeCommand,
    SpyCommand,
    VanishCommand
};

use pocketmine\event\Listener;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\server\CommandEvent;

use pocketmine\player\Player;

class Commands implements Listener
{

    /**
     * @var Core
     */
    private Core $plugin;

    /**
     * Commands constructor.
     */
    public function __construct()
    {
        $this->plugin = Core::getInstance();

        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());

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
                new SizeCommand(),
                new SpyCommand(),
                new KickCommand(),
                new GodCommand(),
                new TrashCommand(),
                new KitCommand(),
                new XboxCommand(),
                new VanishCommand()
            ]
        );
    }

    public function unregisterCommands() : void
    {
        $commands = [
            'list',
            'say',
            'kill',
            'kick'
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
            return;
        }

        if ($player instanceof Player) {
            $this->getPlugin()->getLogger()->info('> ' . $player->getName() . ': /' . $event->getCommand());
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $players) {
                if ($players->isSpy() && $players->getId() != $player->getId()) {
                    $players->sendMessage('(Слежка) ' . $player->getName() . ' ввел: /' . $event->getCommand());
                }
            }
        }
    }
}