<?php


namespace iteplenky\RegUI;


    use iteplenky\RegUI\form\LoginForm;
    use iteplenky\RegUI\form\RegisterForm;

    use BloomLand\Core\BLPlayer;
    
    use pocketmine\player\Player;
    
    use pocketmine\event\Listener;
    
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerQuitEvent;
    use pocketmine\event\player\PlayerChatEvent;
    use pocketmine\event\player\PlayerDropItemEvent;
    use pocketmine\event\player\PlayerPreLoginEvent;
    use pocketmine\event\player\PlayerCommandPreprocessEvent;
    
    use pocketmine\event\block\BlockBreakEvent;
    use pocketmine\event\block\BlockPlaceEvent;
    use pocketmine\event\entity\EntityTeleportEvent;

    use pocketmine\network\NetworkSessionManager;
    use pocketmine\event\entity\ProjectileLaunchEvent;
    use pocketmine\event\inventory\InventoryTransactionEvent;

    class EventListener implements Listener
    {
        private $main;

        private $player;
        
        public function __construct(Main $main)
        {
            $this->main = $main;
        }

        public function getPlugin() : Main
        {
            return $this->main;
        }
        
        /**
         * @param PlayerPreLoginEvent $event
         * @return void
         */
        public function handleLogin(PlayerPreLoginEvent $event) : void
        {    
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
            
                if (strtolower($player->getName()) == strtolower($event->getPlayerInfo()->getUsername())) {
            
                    if (!$this->getPlugin()->isLogined($player->getName())) {
            
                        $player->kick(str_replace('{player}', $event->getPlayerInfo()->getUsername(), $this->getPlugin()->config['error']['isPlayingWithoutPass']));
            
                    } else {
            
                        $event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, (str_replace('{player}', $player->getName(), $this->getPlugin()->config['error']['isPlaying'])));
            
                    }
            
                }
            
            }
        
        }

        
        /**
         * @param PlayerJoinEvent $event
         * @return void
         */
        public function handleJoin(PlayerJoinEvent $event) : void
        {
            $main = $this->main;
            $player = $event->getPlayer();
            $nick = $player->getName();
            
            if (!$main->isRegistered($nick)) {
            
                $player->sendForm(new RegisterForm($main, 'Регистрация'));
                $player->sendMessage($main->config["error"]["login"]);
                $player->setImmobile(true);
                return;
            
            }
            
            if (!$main->isLogined($nick)) {
            
                if ($main->getData($nick)['IpAddress'] == $player->getNetworkSession()->getIp()) {
            
                    $main->setLogined($nick, true);
                    return;
            
                }
                
                $player->setImmobile(true);
                $player->sendForm(new LoginForm($main));
                $player->sendMessage($main->config["error"]["login"]);
            }
        
        }
        
        /**
         * @param PlayerQuitEvent $event
         * @return void
         */
        public function handleQuit(PlayerQuitEvent $event) : void
        {
            if ($this->getPlugin()->isLogined($event->getPlayer()->getName())) {
        
                $this->getPlugin()->setLogined($event->getPlayer()->getName(), false);
        
            }
        
        }
        
        /**
         * @param PlayerChatEvent $event
         * @return void
         */
        public function handleChat(PlayerChatEvent $event) : void
        {
            if (!$this->getPlugin()->isLogined($event->getPlayer()->getName())) {
        
                $event->getPlayer()->sendMessage($this->getPlugin()->config['error']['login']);
                $event->cancel();
        
            }
        
        }
        
        /**
         * @param PlayerCommandPreprocessEvent $event
         * @return void
         */
        public function handleCommandPreprocess(PlayerCommandPreprocessEvent $event) : void
        {
            $player = $event->getPlayer();
                    
            $args = explode(' ', $event->getMessage());
            $cmd = array_shift($args);
        
            if (strpos($cmd, '/') !== false) {
        
                $cmd = str_replace('/', '', $cmd);
        
            }
            
            if (strtolower($cmd) == 'register' or strtolower($cmd) == 'login') return;
            
            if (!$this->getPlugin()->isLogined($player->getName())) {

                $player->sendMessage($this->getPlugin()->config['error']['login']);
                $event->cancel();
            
            }
        
        }
        
        /**
         * @param PlayerDropItemEvent $event
         * @return void
         */
        public function handleDrop(PlayerDropItemEvent $event) : void
        {
            if (!$this->getPlugin()->isLogined($event->getPlayer()->getName())) {
        
                $event->getPlayer()->sendMessage($this->getPlugin()->config['error']['login']);
                $event->cancel();
        
            }
        
        }
        
        /**
         * @param BlockBreakEvent $event
         * @return void
         */
        public function handleBreak(BlockBreakEvent $event) : void
        {
            if (!$this->getPlugin()->isLogined($event->getPlayer()->getName())) {
        
                $event->getPlayer()->sendMessage($this->getPlugin()->config['error']['login']);
                $event->cancel();
        
            }
        
        }
        
        /**
         * @param BlockPlaceEvent $event
         * @return void
         */
        public function handlePlace(BlockPlaceEvent $event) : void
        {
            if (!$this->getPlugin()->isLogined($event->getPlayer()->getName())) {
        
                $event->getPlayer()->sendMessage($this->getPlugin()->config['error']['login']);
                $event->cancel();
        
            }
        
        }

        /**
         * @param InventoryTransactionEvent $event
         * @return void
         */
        public function handleTransaction(InventoryTransactionEvent $event) : void
        {    
            if (!$this->getPlugin()->isLogined($event->getTransaction()->getSource()->getName())) {
        
                $event->getTransaction()->getSource()->sendMessage($this->getPlugin()->config['error']['login']);
                $event->cancel();
        
            }
        
        }

        /**
         * @param ProjectileLaunchEvent $event
         * @return void
         */
        public function handleProjectile(ProjectileLaunchEvent $event) : void
        {
            $thrower = $event->getEntity()->getOwningEntity();
        
            if (!$this->getPlugin()->isLogined($thrower->getName())) $event->cancel();
        
        }

        /**
         * @param EntityTeleportEvent $event
         * @return void
         */
        public function handleTeleport(EntityTeleportEvent $event) : void 
        {
            $entity = $event->getEntity();

            if ($entity instanceof BLPlayer) {
                
                if (!$this->getPlugin()->isLogined($entity->getName())) $event->cancel();

            }
        
        }
    
    }

?>
