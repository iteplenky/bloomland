<?php


namespace BloomLand\Core\controllers;


use JetBrains\PhpStorm\Pure;

use BloomLand\Core\Core;

use BloomLand\Core\command\defaults\{DonateCommand,
    HackCommand,
    IdCommand,
    KitCommand,
    ListCommand,
    CoinsCommand,
    RulesCommand,
    SpawnCommand,
    AfkCommand,
    NearCommand,
    PayCommand,
    SeeMoneyCommand,
    KillCommand,
    RenameCommand,
    CoordsCommand,
    TellCommand,
    TrashCommand,
    XboxCommand};

use BloomLand\Core\command\donators\{BackCommand,
    BlockTopCommand,
    ClearInventoryCommand,
    CookCommand,
    ExtinguishCommand,
    FireCommand,
    FlyCommand,
    GodCommand,
    HealCommand,
    KickCommand,
    MilkCommand,
    NightVisionCommand,
    OffDropCommand,
    SayCommand,
    RepairCommand,
    SizeCommand,
    SkinCommand,
    SpeedCommand,
    SpyCommand,
    TopCommand,
    VanishCommand,
    VanishListCommand};

use BloomLand\Core\command\admin\RestartCommand;

use pocketmine\event\Listener;

use pocketmine\event\player\{
    PlayerCommandPreprocessEvent,
    PlayerDeathEvent,
    PlayerRespawnEvent
};

use pocketmine\event\server\CommandEvent;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\inventory\InventoryPickupItemEvent;

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
                new VanishCommand(),
                new BlockTopCommand(),
                new ExtinguishCommand(),
                new MilkCommand(),
                new NightVisionCommand(),
                new OffDropCommand(),
                new SkinCommand(),
                new SpeedCommand(),
                new VanishListCommand(),
                new RestartCommand(),
                new DonateCommand(),
                new HackCommand(),
                new IdCommand(),
                new RulesCommand(),
                new TellCommand(),
                new BackCommand(),
                new CookCommand(),
                new FireCommand(),
                new TopCommand()
            ]
        );
    }

    public function unregisterCommands() : void
    {
        $commands = [
            'list',
            'say',
            'kill',
            'kick',
            'ban',
            'ban-ip',
            'banlist',
            'clear',
            'defaultgamemode',
            'deop',
            'difficulty',
            'dumpmemory',
            'effect',
            'enchant',
            'extractplugin',
            'gc',
            'genplugin',
            'makeplugin',
            'me',
            'op',
            'pardon',
            'pardon-ip',
            'particle',
            'seed',
            'setworldspawn',
            'spawnpoint',
            'status',
            'stop',
            'tell',
            'title',
            'transferserver',
            'version',
            'whitelist'
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
            $player->sendMessage('Использование команды §cограничено для использования§r.');
            return;
        }

        if ($player instanceof Player) {
            $this->getPlugin()->getLogger()->info('> §3' . $player->getName() . '§r: /§b' . $event->getCommand());
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $players) {
                if ($players->isSpy() && $players->getId() != $player->getId()) {
                    $players->sendMessage('§7(§2Слежка§7) §3' . $player->getName() . '§r ввел: §b/' . $event->getCommand());
                }
            }
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    #[Pure]
    public function handlePlayerPreProccess(PlayerCommandPreprocessEvent $event) : void
    {
        $message = $event->getMessage();
        $player = $event->getPlayer();

        if (preg_match("#^\/([\/a-z0-9_-а-яё@\.,:;'\"]+)[ ]?(.*)$#i", $message, $matches) > 0) {
            if (is_null($this->getPlugin()->getServer()->getCommandMap()->getCommand(
                $command = strtolower($matches[1])
            ))) {
                $player->sendMessage('Команда §b' . $matches[1] . ' §rне найдена.');
                $event->cancel();
            }
        }
    }

    /**
     * @param InventoryPickupItemEvent $event
     */
    public function handlePickUp(InventoryPickupItemEvent $event) : void
    {
        foreach ($event->getInventory()->getViewers() as $player) {
            if ($player->isOffDrop()) {
                $event->cancel();
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function handlePlayerDeath(PlayerDeathEvent $event) : void
    {
        $player = $event->getPlayer();

        if ($player->hasPermission('core.command.back')) {
            $player->setBackPosition($player->getLocation()->asVector3());
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     */
    public function handleRespawnEvent(PlayerRespawnEvent $event) : void
    {
        $player = $event->getPlayer();

        if ($player->hasPermission('core.command.back')) {
            $player->sendMessage('Вы можете §bвернуться §rна место §cсмерти§r.');
        }
    }
}