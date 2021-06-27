<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\AntiCheat;
    use BloomLand\Core\task\HealthTask;
    use BloomLand\Core\task\PickupTask;
    use BloomLand\Core\item\Hammer;

    use BloomLand\Core\sqlite3\SQLite3;
 
    use BloomLand\Chat\ChatFilter; 
    use BloomLand\Core\base\Ban;

    use BloomLand\Core\base\Economy;

    use BloomLand\Core\utils\API;
    use pocketmine\world\sound\AnvilUseSound;

    use pocketmine\event\Listener;

    use pocketmine\block\VanillaBlocks;
    // use pocketmine\math\Vector3;

    use pocketmine\event\player as event;

	use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\utils\TextFormat;

    use pocketmine\network\mcpe\protocol\OnScreenTextureAnimationPacket;
    use BloomLand\Core\scoreboard\ScoreboardFactory;
    use pocketmine\math\Vector3;
    use pocketmine\item\VanillaItems;
    use pocketmine\block\BlockLegacyIds;
    use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
    use pocketmine\network\mcpe\protocol\types\RuntimeBlockMapping;
    use pocketmine\network\mcpe\protocol\types\DeviceOS;
    use pocketmine\network\mcpe\protocol\LoginPacket;
    use pocketmine\network\mcpe\protocol\ProtocolInfo;
    use pocketmine\event\block\BlockBreakEvent;
    use pocketmine\item\ItemFactory;
    use pocketmine\block\BlockFactory;
    use pocketmine\Server;
    use pocketmine\player\GameMode;
    use pocketmine\command\ConsoleCommandSender;
    use pocketmine\event\server\DataPacketReceiveEvent;
    use pocketmine\network\mcpe\protocol\EmotePacket;
    use pocketmine\event\server\CommandEvent;
    use pocketmine\event\server\DataPacketSendEvent;
    use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
    use pocketmine\network\mcpe\protocol\TextPacket;
    use _64FF00\PurePerms\PurePerms;
    use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
    use pocketmine\scheduler\ClosureTask;




    use muqsit\fakeplayer\Loader;

use pocketmine\resourcepacks\ResourcePack;
use pocketmine\resourcepacks\ResourcePackManager;

use pocketmine\network\mcpe\protocol\types\resourcepacks\ResourcePackType;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
    
    class PlayerListener implements Listener
    {
        private $afk;
        private $plugin;
        private $time = [];

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
            // if ($event->getPlayerInfo()->getUsername() != 'iteplenky8501') {

            //     $event->setKickReason(1, '§eсервер в режиме разработки..');
                
            // } 

            // print_r($event->getPlayerInfo()->getExtraData());

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
				'Orbis',
				'NX'
			];
			$this->devices[$user] = $device[$deviceOS] ?? 'Неизвестно';
            
            if (Ban::isBanned($user)) 
                $event->setKickReason(1, '§l| §rВы заблокированы игроком §a' . Ban::get($user, 'sender') . '§7.' . PHP_EOL . 
                '§l| §r§fПричина: §e' . Ban::get($user, 'reason') . PHP_EOL . PHP_EOL . '§l| §rОставить жалобу: §bvk.com/bl_pe');
            
        }

        public function handleJoin(event\PlayerJoinEvent $event) : void
        {
            $player = $event->getPlayer();

            $event->setJoinMessage(null);

            $ping = $player->getNetworkSession()->getPing();

            $status = ' §cОчень Плохой';

            if ($ping < 60)
                $status = ' §aОтличный';
                
            elseif ($ping < 140)
                $status = ' §cХороший';
                
            elseif ($ping < 250)
                $status = ' §eНестабильный';
                
            elseif ($ping < 400)
                $status = ' §cПлохой';

            $this->getPlugin()->getServer()->broadcastPopup(' §f[§b+§f] §f' . $player->getName());
            $this->getPlugin()->getLogger()->info('§7Игрок §b' . $player->getName() . ' §7зашел со статусом ' . $status . '§7.');
            
            if ($player instanceof BLPlayer) {

                // $pp = Core::getAPI()->getServer()->getPluginManager()->getPlugin('PurePerms');

                // $pp->updatePermissions($player);

                $player->loadPlayer($this->getPlugin()->getDatabase());
                
                // $player->setLanguage(SQLite3::getStringValue($player->getLowerCaseName(), 'lang'));

                $player->joinTime = time();
                $player->device = $this->devices[$player->getLowerCaseName()];

                unset($this->devices[$player->getLowerCaseName()]);
               
                $player->sendMessage(' ');
                $player->sendMessage(' ');

                $player->sendMessage('  ');
                
                $player->sendMessage(' ');
                $player->sendMessage(' ');
                $player->sendMessage(' ');

                $cases = [2, 0, 1, 1, 1, 2];
                
                $after = [' §rигрок', ' §rигрока', ' §rигроков'];
                
                $number = count($this->getPlugin()->getServer()->getOnlinePlayers());
               
                $player->sendMessage(' §r> Сейчас в сети: §b' . $number . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]] . '.');
                
                $player->sendMessage(' ');
                
                $player->sendMessage(' §r> Ваш статус подключения: ' . $status . '§r. §8(§7' . $ping . '§8)');
                
                $player->sendMessage(' ');
                $player->sendMessage(' §r> Добро пожаловать на сервер ');
                $player->sendMessage(' ');


                // $player->sendTitle('', '', 20, 30, 5);

                // $player->getLocation()->getWorld()->addSound($player->getLocation(), new GenericSound(LevelSoundEventPacket::SOUND_RECORD_CHIRP)); 
                //SOUND_RECORD_CHIRP // mal //stal
                
                $pk = new OnScreenTextureAnimationPacket();
                $pk->effectId = 27; 
                $player->getNetworkSession()->sendDataPacket($pk);

                if ($player->isConnected()) {

                    ScoreboardFactory::createScoreboard($player);

                }

                // Core::getAPI()->getScheduler()->scheduleDelayedTask(new HealthTask($player), 1); 


            }

        }

        // public function handlePickBlock(PlayerBlockPickEvent $event) : void 
        // {
        //     $event->getPlayer()->sendPopup('§bскопировано');
        // }

        // public function handleSkinChange(event\PlayerChangeSkinEvent $event) : void
        // {
            // $player = $event->getPlayer();

            // $event->cancel();
        // }

        public function handlePlayerExhaust(event\PlayerExhaustEvent $event) : void 
        {
            $event->setAmount($event->getAmount() / 2);
        }

        public function handleDamageByEntity(EntityDamageByEntityEvent $event) : void
        {
            $entity = $event->getEntity();

            if ($entity instanceof BLPlayer and $event->getDamager() instanceof BLPlayer and $event->getDamager()->getId() !== $entity->getId())
                $entity->setLastAttacker($event->getDamager());
        }

        public function handleEntityDamageEvent(EntityDamageEvent $event) : void
        {
            if ($event->getCause() !== EntityDamageEvent::CAUSE_SUFFOCATION)
                return;

            $entity = $event->getEntity();
            if (!$entity instanceof BLPlayer)
                return;

            $world = $entity->getWorld();
            $vec = $entity->getPosition()->floor();

            foreach ([2,3,4,5] as $_ => $face) {
                
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
            $explode = explode(" ", $event->getCommand());
            $commands = $this->getPlugin()->getServer()->getCommandMap()->getCommands();
        
            if (isset($commands[$explode[0]]))
                return;
    
            foreach ($commands as $key => $value) {

                if (strcasecmp($explode[0], $key) === 0) {

                    $explode[0] = $key;
                    break;
                
                }
            
            }
            
            $event->setCommand(implode(" ", $explode));
        }

        public function handleBlockBreak(BlockBreakEvent $event) : void
        {
            $player = $event->getPlayer();
        
            if (!$player->hasFiniteResources())
                return;
    
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

        public $ResourcePackManager = null;//behaviorPack
        /** @var bool */
        public $IsExperimentalGamePlay = true;

        public function resourcepackManager(DataPacketSendEvent $event) : void
        {
            $packets = $event->getPackets();
            
            foreach ($packets as $pk) {


                if ($pk instanceof ResourcePackStackPacket) {

                    // echo ' > ResourcePackStackPacket';

                    // $pk->experiments = $this->IsExperimentalGamePlay;
                    // $pk->behaviorPackStack = $this->ResourcePackManager->getResourceStack();
                    
                } else if ($pk instanceof ResourcePacksInfoPacket) {

                    // echo ' ! ResourcePacksInfoPacket';

                    // $pk->mustAccept = false;
                    // $pk->behaviorPackEntries = $this->ResourcePackManager->getResourceStack();
                    // $pk->hasScripts = true; // интересная тема
                
                } else if ($pk instanceof ResourcePackDataInfoPacket) {

                    // echo ' ? ResourcePackDataInfoPacket';
                    $pk->packType = ResourcePackType::RESOURCES;
                    $pk->isPremium = true;
                    // $pk->sha256 = '06bdec003a999280f88b66160d408986dccd876632c23cf4491760cf99f4329e';
                } else if ($pk instanceof ResourcePackChunkDataPacket) {

                    // echo $pk->progress . '% ';
                }
         
            }
        
        }

        public function handleManager(DataPacketReceiveEvent $event) : void
        {
            $pk = $event->getPacket();
        
            if ($pk instanceof ResourcePackClientResponsePacket) {

                var_dump($pk->packIds);

            }

            
            if ($pk instanceof LoginPacket) {

                // echo $pk->protocol;
                
                // print $pk->chainDataJwt;
                // echo $pk->clientDataJwt;

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
            return preg_replace("/%*(([a-z0-9_]+\.)+[a-z0-9_]+)/i", "%$1", $str) . '§　';
        }

        public function handleQuit(event\PlayerQuitEvent $event) : void
        {
            $player = $event->getPlayer();
            $name = $player->getName();

            // Gambler::setConnect($name, "offline");
            $event->setQuitMessage(null);

            AntiCheat::removeFromAntiCheats($player);
            
            // SessionFactory::removeSession($player);
			ScoreboardFactory::removeSecoreboard($player);
            
        }

    }

?>
