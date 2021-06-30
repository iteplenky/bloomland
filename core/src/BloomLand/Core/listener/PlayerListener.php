<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\{
        Core,
        BLPlayer,
        AntiCheat,

        base\Ban,

        scoreboard\ScoreboardFactory,

        task\PickupTask,
        task\TagTask,

        item\Hammer,

        utils\API
    };
  
    use pocketmine\event\Listener;

    use pocketmine\event\player as event;

    
    use pocketmine\event\server\{
        DataPacketReceiveEvent,
        DataPacketSendEvent
    };
    
    use pocketmine\network\mcpe\protocol\{
        TextPacket,
        AvailableCommandsPacket,
        ResourcePackDataInfoPacket,
        EmotePacket
    };
    
    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    use pocketmine\event\block\BlockBreakEvent;
    
    use pocketmine\event\server\CommandEvent;

    class PlayerListener implements Listener
    {
        private $plugin;
        
        private $afk;
        
        public static $first, $tm, $new = []; 
        
        /** @var string[] */
        private $devices = [];

        public function __construct()
        {
            $this->plugin = Core::getAPI();
            $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
        }

        private function getPlugin(): Core
        {
            return $this->plugin;
        }

        public function handlePlayerCreation(event\PlayerCreationEvent $event) : void
        {
            $event->setPlayerClass(BLPlayer::class);
        }

        public function handlePreLogin(event\PlayerPreLoginEvent $event) : void
        {
            $user = strtolower($event->getPlayerInfo()->getUsername());

            $deviceOS = $event->getPlayerInfo()->getExtraData()['DeviceOS'];

            static $device = [
                'Неизвестно',
                'Android',
                'iOS',
                'macOS',
                'FireOS',
                'GearVR',
                'HoloLens',
                'Windows 10',
                'Windows',
                'Dedicated',
                'tvOS',
                'PlayStation',
                'Nintendo',
                'Xbox',
                'Windows Phone'
            ];
            $this->devices[$user] = $device[$deviceOS] ?? 'Неизвестно';
            
            if (Ban::isBanned($user)) {

                $event->setKickReason(1, '§l| §rВы заблокированы игроком §a' . Ban::get($user, 'sender') . '§7.' . PHP_EOL . 
                '§l| §r§fПричина: §e' . Ban::get($user, 'reason') . PHP_EOL . PHP_EOL . '§l| §rОставить жалобу: §bvk.com/bl_pe');

            }
            
        }

        public function handleJoin(event\PlayerJoinEvent $event) : void
        {
            $event->setJoinMessage(null);
            
            $player = $event->getPlayer();

            $ping = $player->getNetworkSession()->getPing();

            $status = ' §cОчень Плохой';

            if ($ping < 60) $status = ' §aОтличный';
                
            elseif ($ping < 140) $status = ' §cХороший';
                
            elseif ($ping < 250) $status = ' §eНестабильный';
                
            elseif ($ping < 400) $status = ' §cПлохой';

            $this->getPlugin()->getServer()->broadcastPopup(' §f[§b+§f] §f' . $player->getName());

            $this->getPlugin()->getLogger()->info('§7Игрок §b' . $player->getName() . ' §7зашел со статусом ' . $status . '§7.');
            
            if ($player instanceof BLPlayer) {

                $player->joinTime = time();
                $player->device = $this->devices[$player->getLowerCaseName()];

                unset($this->devices[$player->getLowerCaseName()]);

                $cases = [2, 0, 1, 1, 1, 2];
                
                $after = [' §rигрок', ' §rигрока', ' §rигроков'];
                
                $number = count($this->getPlugin()->getServer()->getOnlinePlayers());

                $player->loadPlayer($this->getPlugin()->getDatabase());
                
                // $player->setLanguage(SQLite3::getStringValue($player->getLowerCaseName(), 'lang'));
               
                $player->sendMessage(' ');
                $player->sendMessage(' ');

                $player->sendMessage('  ');
                
                $player->sendMessage(' ');
                $player->sendMessage(' ');
                $player->sendMessage(' ');
               
                $player->sendMessage(' §r> Сейчас в сети: §b' . $number . 
                $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]] . '.');
                
                $player->sendMessage(' ');
                
                $player->sendMessage(' §r> Ваш статус подключения: ' . $status . '§r. §8(§7' . $ping . '§8)');
                
                $player->sendMessage(' ');
                $player->sendMessage(' §r> Добро пожаловать на сервер ');
                $player->sendMessage(' ');

                ScoreboardFactory::createScoreboard($player);

                $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new TagTask($player, $this), 20);

            }

        }

        public function handlePlayerExhaust(event\PlayerExhaustEvent $event) : void 
        {
            $event->setAmount($event->getAmount() / 5);
        }

        public function handleDamageByEntity(EntityDamageByEntityEvent $event) : void
        {
            $entity = $event->getEntity();
            $damager = $event->getDamager();

            if ($entity instanceof BLPlayer and $damager instanceof BLPlayer and $damager->getId() !== $entity->getId()) {

                $entity->setLastAttacker($damager);

            }

        }

        public function handleEntityDamageEvent(EntityDamageEvent $event) : void
        {
            if ($event->getCause() !== EntityDamageEvent::CAUSE_SUFFOCATION) return;

            $entity = $event->getEntity();

            if (!$entity instanceof BLPlayer) return;

            $world = $entity->getWorld();
            $vec = $entity->getPosition()->floor();

            foreach ([2, 3, 4, 5] as $_ => $face) { // ?vector sides
                
                $blockVec = $vec->getSide($face);

                if ($world->getBlock($blockVec->up())->getId() === 0 && $world->getBlock($blockVec)->getId() === 0) {
                   
                    $entity->setMotion($blockVec->subtract($vec->x, $vec->y, $vec->z)->multiply(0.1));
                    $event->cancel();
                    return;
            
                }

            }

        }

        public function handleCommandEvent(CommandEvent $event) : void
        {
            $explode = explode(' ', $event->getCommand());
            $commands = $this->getPlugin()->getServer()->getCommandMap()->getCommands();
        
            if (isset($commands[$explode[0]]))
                return;
    
            foreach ($commands as $key => $value) {

                if (strcasecmp($explode[0], $key) === 0) {

                    $explode[0] = $key;
                    break;
                
                }
            
            }
            
            $event->setCommand(implode(' ', $explode));
        }

        public function handleBlockBreak(BlockBreakEvent $event) : void
        {
            $player = $event->getPlayer();
        
            if (!$player->hasFiniteResources()) return;
    
            $blockPos = $event->getBlock()->getPos();

            foreach ($event->getDrops() as $drop) 
                $this->getPlugin()->getScheduler()->scheduleDelayedTask(new PickupTask($player, $drop, $blockPos), 10);
            
            $event->setDrops([]);
        }

        
        public function handleRespawn(event\PlayerRespawnEvent $event) : void
        {
            $config = $this->getPlugin()->getConfig();
            
            $event->setRespawnPosition(API::unpackRawLocation($config->get('spawnPosition')));
        }
        
        public function handleEmotePacket(DataPacketReceiveEvent $event) : void
        {
            $packet = $event->getPacket();
        
            if ($packet instanceof EmotePacket) {

                $emoteId = $packet->getEmoteId();

                $this->getPlugin()->getServer()->broadcastPackets($event->getOrigin()->getPlayer()->getViewers(), [
                    EmotePacket::create($event->getOrigin()->getPlayer()->getId(), $emoteId, 1 << 0)
                ]);
            
            }
        
        }

        public function handleResourcesManager(DataPacketSendEvent $event) : void
        {
            $packets = $event->getPackets();
            
            foreach ($packets as $pk) {
                
                if ($pk instanceof ResourcePackDataInfoPacket) {

                    $pk->isPremium = true;

                } 
         
            }
        
        }
        
        public function handlePacketSend(DataPacketSendEvent $event) : void
        {
            $packets = $event->getPackets();
            
            foreach ($packets as $pk) {
            
                if ($pk instanceof StartGamePacket) 
                    $pk->itemTable = Hammer::$entries;
                
                else {

                    if ($pk instanceof TextPacket) {
                        
                        if ($pk->type === TextPacket::TYPE_TIP || 
                            $pk->type === TextPacket::TYPE_POPUP || 
                            $pk->type === TextPacket::TYPE_JUKEBOX_POPUP
                            )
                            continue;
        
                        if ($pk->type === TextPacket::TYPE_TRANSLATION) 
                            $pk->message = $this->toThin($pk->message);

                        else $pk->message .= '§　';
                        
                    } elseif ($pk instanceof AvailableCommandsPacket) {

                        foreach ($pk->commandData as $name => $commandData) 
                            $commandData->description = $this->toThin($commandData->description);

                    }

                }

            }
    
        }

        private function toThin(string $str) : string
        {
            return preg_replace('/%*(([a-z0-9_]+\.)+[a-z0-9_]+)/i', '%$1', $str) . '§　';
        }

        public function handleQuit(event\PlayerQuitEvent $event) : void
        {
            $player = $event->getPlayer();

            $event->setQuitMessage(null);

            AntiCheat::removeFromAntiCheats($player);
            
            ScoreboardFactory::removeSecoreboard($player);
            
        }

    }

?>
