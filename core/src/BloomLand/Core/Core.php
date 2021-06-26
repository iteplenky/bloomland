<?php 


namespace BloomLand\Core;


    use BloomLand\Core\plugin\BloomLandPlugin;

    use BloomLand\Core\lang\Language;

    use BloomLand\Chat\ChatFilter;

    use BloomLand\Core\utils\TextUtils;
    
    use BloomLand\Core\scoreboard\ScoreboardFactory;

    use BloomLand\Core\task\{
        CleanerTask, 
        HealthTask, 
        ChatGameTask,
        PlantRespawnTask,
        RainbowTask
    };

    use BloomLand\Core\commands\{
        player\CoinsCommand, 
        player\SpawnCommand,
        player\PayCommand,
        player\TopCoinsCommand,
        // DeathPosCommand,
        player\KitCommand,
        player\TellCommand,
        player\ReplyCommand,
        // RegionCommand,
        player\SeeMoneyCommand,
        player\ListCommand,
        player\OffDropCommand,
        player\RelaxCommand,

        settings\LangCommand,
        settings\PingCommand,
        settings\XYZCommand,

        staff\StopCommand,
        staff\NpcCommand,
        staff\BanCommand,
        staff\TimeCommand,
        staff\RepairCommand,
        staff\RenameCommand,
        staff\HealCommand,
        staff\GamemodeCommand,
        staff\FlyCommand,
        staff\ClearInventoryCommand,
        staff\SayCommand,
        staff\KillCommand,
        staff\TeleportCommand,
        staff\NewPlayerCommand,
        staff\SkinCommand,
        staff\ToSpawnCommand,
        staff\SizeCommand,
        staff\KickCommand,
        staff\ConfettiCommand,
        staff\KingCommand,
        // staff\RunCommand,
        staff\PardonCommand
    };
    
    use BloomLand\Core\listener\{
        PlayerListener,
        ChatGameListener,
        ChatManager,
        CommandBlock,
        PlayerDeath,
        PlayerSwimming,
        SpawnListener,
        SpyManager,
        CombatListener
    };
    
    use BloomLand\Core\entity\{
        MoneyCrate,
        Booster,
        OverClock,
        Bin,
        Quester,
        Buyer,
        Yoda,
        Head,
        DonateCrate
    };

    use BloomLand\Core\item\{
        Hammer
    };
    
    use pocketmine\entity\{
        Human, 
        EntityFactory, 
        EntityDataHelper
    };
    
    use pocketmine\world\World;
    use pocketmine\nbt\tag\CompoundTag;
    
    use pocketmine\scheduler\Task;

    use pocketmine\utils\Config;

    use pocketmine\item\Item;
    use pocketmine\item\ItemFactory;
    use pocketmine\item\ItemIdentifier;
    
    use pocketmine\network\mcpe\convert\ItemTranslator;
        
    class Core extends BloomLandPlugin 
    {
        const PREFIX = '§r > ';
        
        public const SERVER_NAME_FORMAT = '§l§bBloom§fLand §7» ';
        
	    private static $api;
        
        protected $chatFilter;

        protected $lastTalkers;
        
        protected static $config;

        protected static $status = false;
        
        protected static \SQLite3 $database;

        public function onLoad() : void 
        {
            self::$api = $this;

            self::defineDatabase();
            self::unloadCommands();
        }
        
        public function onEnable() : void
        {
            parent::onEnable();
            
            $this->saveDefaultConfig();
            self::$config = self::getAPI()->getConfig()->getAll();
            
            Language::init();
            ScoreboardFactory::init();
            
            $this->chatFilter = new ChatFilter($this);
            
            self::getAPI()->updateServerName();
            
            self::getAPI()->initEntity();
          
            self::getAPI()->loadTasks();

            self::getAPI()->loadEvents();

            self::getAPI()->loadCommands();

            self::getAPI()->loadItems();

            // http://patorjk.com/software/taag/#p=display&f=Big&t=BLOOMLAND
            $logo =
            PHP_EOL . '§b§l' . "  ____  _      ____   ____  __  __ " . '§d§l' . "_               _   _ _____  ".
            PHP_EOL . '§b§l' . " |  _ \| |    / __ \ / __ \|  \/  |" . '§d§l' . " |        /\   | \ | |  __ \ ".
            PHP_EOL . '§b§l' . " | |_) | |   | |  | | |  | | \  / |" . '§d§l' . " |       /  \  |  \| | |  | |".
            PHP_EOL . '§b§l' . " |  _ <| |   | |  | | |  | | |\/| |" . '§d§l' . " |      / /\ \ | . ` | |  | |".
            PHP_EOL . '§b§l' . " | |_) | |___| |__| | |__| | |  | |" . '§d§l' . " |____ / ____ \| |\  | |__| |".
            PHP_EOL . '§b§l' . " |____/|______\____/ \____/|_|  |_|" . '§d§l' . "______/_/    \_\_| \_|_____/ ".
            PHP_EOL;

            $this->getLogger()->info($logo);

            $db = self::getDatabase();
            $db->query("CREATE TABLE IF NOT EXISTS `data` (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `username` text, `coins` int, `lang` text)");
            $db->query("CREATE TABLE IF NOT EXISTS `intruders` (`username` text, `sender` text, `reason` text)");

            self::$status = true;
        }

        public function onDisable() : void
		{
            self::$status = false;
		}

        public function getStatus() : bool 
        {
            return self::$status;
        }

        public static function getAPI() : self 
        { 
            return self::$api; 
        }

        public static function getPrefix() : string
        {
            return self::PREFIX;
        }

        public function getChatFilter() : ChatFilter 
        { 
            return $this->chatFilter; 
        }
        
        public static function getDatabase() : \SQLite3 
        { 
            return self::$database; 
        }
        
        private static function defineDatabase() : void 
        { 
            self::$database = new \SQLite3(self::getAPI()->getDataFolder() . 'users.db'); 
        }
        
        public static function getResourcesPath() : string 
        { 
            return self::getAPI()->getFile() . '/resources/'; 
        }

        private function loadTasks() : void 
        {
            $scheduler = self::getAPI()->getScheduler();

            $scheduler->scheduleRepeatingTask(new CleanerTask(self::getAPI()->getServer()->getWorldManager()), 20 * 60 * 3); // 3 min
            $scheduler->scheduleRepeatingTask(new ChatGameTask(), 20 * 60 * 10); // 10 min
        }

        private function loadEvents() : void
        {
            new PlayerListener();
            new AntiCheat();
            new ChatGameListener();
            new ChatManager();
            new CommandBlock();
            new PlayerDeath();
            new PlayerSwimming();
            new SpawnListener();
            new SpyManager();
            new CombatListener();
        }

        private function loadCommands() : void
        {
            $this->getServer()->getCommandMap()->registerAll(self::getAPI()->getName(), 
            [
                new CoinsCommand(),
                new SpawnCommand(),
                new LangCommand(),
                new StopCommand(),
                new XYZCommand(),
                new PayCommand(),
                new TopCoinsCommand(),
                new NpcCommand(),
                // new DeathPosCommand(),
                new BanCommand(),
                new PingCommand(),
                new TimeCommand(),
                new RepairCommand(),
                new RenameCommand(),
                new HealCommand(),
                new GamemodeCommand(),
                new FlyCommand(),
                new ClearInventoryCommand(),
                new SayCommand(),
                new KitCommand(),
                new KillCommand(),
                new TellCommand(),
                new ReplyCommand(),
                new TeleportCommand(),
                // new RegionCommand(),
                new SeeMoneyCommand(),
                new NewPlayerCommand(),
                new SkinCommand(),
                new ToSpawnCommand(),
                new SizeCommand(),
                new KickCommand(),
                new ListCommand(),
                new OffDropCommand(),
                new ConfettiCommand(),
                new KingCommand(),
                new RelaxCommand(),
                // new RunCommand(),
                new PardonCommand()
            ]);
        }

        private function initEntity() : void
        {
            $factory = EntityFactory::getInstance();
            
           
        }

        private function loadItems() : void 
        {
            self::getAPI()->saveResource("id.json");

            ItemFactory::getInstance()->register(new Item(new ItemIdentifier(Hammer::$id, 0), Hammer::$name));

            Hammer::init(self::getAPI());

            $instance = ItemTranslator::getInstance();
            $ref = new \ReflectionObject($instance);
            $r1 = $ref->getProperty("simpleCoreToNetMapping");
            $r2 = $ref->getProperty("simpleNetToCoreMapping");
            $r1->setAccessible(true);
            $r2->setAccessible(true);
            $r1->setValue($instance, Hammer::$simpleCoreToNetMapping);
            $r2->setValue($instance, Hammer::$simpleNetToCoreMapping);
        }

        private function unloadCommands() : void
        {
            $commands = [
                "gamemode",
                "?",
                "help",
                "clear",
                "listperms",
                "mixer",
                "gc",
                "stop",
                "title",
                "tell",
                "w",
                "msg",
                // "give",
                "ban",
                "ban-ip",
                "pardon",
                "pardon-ip",
                "tp",
                "teleport",
                "whitelist",
                "kill",
                // "enchant",
                // "xp",
                "kick",
                "me",
                "say",
                "list",
                "banlist",
                "spawnpoint",
                // "setworldspawn",
                // "status",
                "time",
                "extractplugin",
                "makeplugin",
                "genplugin",
                "pl",
                // "ver",
                // "version",
                // "about",
                "plugins",
                "transferserver",
                "makeserver",
                "particle",
                // "effect",
                "difficulty",
                "checkperm",
                "defaultgamemode",
                // "op",
                // "deop",
                "mixer",
                // "reload",
                // "timings",
                "seed",
                // "save-all",
                // "save-on",
                // "save-off"
            ];

            $map = self::getAPI()->getServer()->getCommandMap();

            foreach ($commands as $cmd) {

                $command = $map->getCommand($cmd);

                if ($command !== null) {

                    $command->setLabel("old_" . $cmd);
                    $map->unregister($command);
                }
            }
        }

        public function updateServerName() : void
        {
            if (self::getAPI()->getServer()->hasWhitelist()) 
                $status = '§eDEV';
            
            elseif (TextUtils::inText('BETA', self::getAPI()->getDescription()->getVersion()))
                $status = '§cBETA';

            else
                $status = '§r§7v§7' . self::getAPI()->getDescription()->getVersion();
            
            self::getAPI()->getServer()->getNetwork()->setName(self::SERVER_NAME_FORMAT . '§aSurvival §l' . $status . '§r');
        }

        public function setLastTalkers($player, $target) : void
        {
            self::getAPI()->lastTalkers[$player] = $target;
            self::getAPI()->lastTalkers[$target] = $player;
        }

    }

?>