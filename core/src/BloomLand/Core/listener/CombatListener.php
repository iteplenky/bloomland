<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\event\Listener;

    use pocketmine\item\VanillaItems;

    use pocketmine\event\player\PlayerCommandPreprocessEvent;
    use pocketmine\event\player\PlayerItemConsumeEvent;
    use pocketmine\event\player\PlayerQuitEvent;
    use pocketmine\event\player\PlayerInteractEvent;
    use pocketmine\event\player\PlayerDeathEvent;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityTeleportEvent;
    use pocketmine\event\entity\ProjectileLaunchEvent;

    use pocketmine\entity\projectile\EnderPearl;

    class CombatListener implements Listener 
    {
        /** @var int[] */
        public $godAppleCooldown = [];

        /** @var int[] */
        public $goldenAppleCooldown = [];

        /** @var int[] */
        public $enderPearlCooldown = [];

        private const WHITELISTED = [
            '/gamemode',
            '/tp',
            '/ban',
            '/kick',
            '/spawn'
        ];
        
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handleCommandPreprocess(PlayerCommandPreprocessEvent $event) : void 
        {
            $player = $event->getPlayer();

            if ($player instanceof BLPlayer) {

                if (strpos($event->getMessage(), '/') !== 0) {

                    return;
    
                }
    
                if (in_array(explode(' ', $event->getMessage())[0], self::WHITELISTED)) {
    
                    return;
    
                }
    
                if ($player->isTagged()) {
    
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы не можете §bиспользовать§r команды во время сражения.');
                    $event->cancel();
    
                }

            }

        }

        public function handleItemConsume(PlayerItemConsumeEvent $event) : void 
        {
            $player = $event->getPlayer();
            $item = $event->getItem();

            if ($player->isTagged()) {
        
                if ($item->equals(VanillaItems::ENCHANTED_GOLDEN_APPLE(), false, false)) {

                    if (isset($this->godAppleCooldown[$player->getUniqueId()->toString()])) {

                        if ((time() - $this->godAppleCooldown[$player->getUniqueId()->toString()]) < 40) {

                            if (!$event->isCancelled()) {

                                $time = 40 - (time() - $this->godAppleCooldown[$player->getUniqueId()->toString()]);
                                $player->sendMessage(Core::getAPI()->getPrefix() . '§bПомедленнее§r! До следующего раза осталось: §b' . $time . ' §rсекунд.');
                                $event->cancel();
                                return;

                            }

                        }

                        $this->godAppleCooldown[$player->getUniqueId()->toString()] = time();
                        return;
                    }

                    $this->godAppleCooldown[$player->getUniqueId()->toString()] = time();
                    return;
                }

                if ($item->equals(VanillaItems::GOLDEN_APPLE(), false, false)) {

                    if (isset($this->goldenAppleCooldown[$player->getUniqueId()->toString()])) {

                        if ((time() - $this->goldenAppleCooldown[$player->getUniqueId()->toString()]) < 20) {

                            if (!$event->isCancelled()) {

                                $time = 20 - (time() - $this->goldenAppleCooldown[$player->getUniqueId()->toString()]);
                                $player->sendMessage(Core::getAPI()->getPrefix() . '§bПомедленнее§r! До следующего раза осталось: §b' . $time . ' §rсекунд.');
                                $event->cancel();
                                return;

                            }

                        }

                        $this->goldenAppleCooldown[$player->getUniqueId()->toString()] = time();
                        return;
                    }

                    $this->goldenAppleCooldown[$player->getUniqueId()->toString()] = time();
                    return;
                }

            }

        }

        public function handlePlayerDeath(PlayerDeathEvent $event) : void 
        {
            $event->getPlayer()->combatTag(false);
        }

        public function handlePlayerQuit(PlayerQuitEvent $event) : void 
        {
            $player = $event->getPlayer();
            
            if ($player->isTagged()) {

                if (Core::getAPI()->getStatus()) {

                    $player->setHealth(0);

                } else $player->combatTag(false);

            }

        }

        public function pearlLaunch(ProjectileLaunchEvent $event) : void
        {
            if ($event->isCancelled()) return;

            $player = $event->getEntity()->getOwningEntity();
        
            if ($event->getEntity() instanceof EnderPearl) {

                if ($player->isTagged()) {

                    $time = 40 - (time() - $this->enderPearlCooldown[$player->getUniqueId()->toString()]);
                    $player->sendMessage(Core::getAPI()->getPrefix() . '§bОпаньки§r! Вы не можете убегать со сражения.');
                    $event->cancel();

                } else {

                    $event->cancel();

                }

            }

        }

        public function handleEntityDamage(EntityDamageEvent $event) : void 
        {
            if ($event->isCancelled()) return;

            $entity = $event->getEntity();
            
            if ($entity instanceof BLPlayer) {
            
                if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {

                    $event->cancel();
                    return;

                }

                if ($event instanceof EntityDamageByEntityEvent) {

                    $damager = $event->getDamager();
                    
                    if (!$damager instanceof BLPlayer) {
                    
                        return;
                    
                    }
                    
                    if ($damager->isVanished()) {

                        $event->cancel();
                        return;
                    }
                    
                    if ($entity->isVanished()) {
                     
                        $event->cancel();
                        
                        if ($damager->isOp()) {
                         
                            $damager->sendMessage(Core::getAPI()->getPrefix() . 'В невидимости игрок - §b' . $entity->getName() . '§r.');
                        }
                            
                        return;
                    }

                    if ($damager->isCreative() or $entity->isCreative()) {

                        $event->cancel();
                        return;

                    }

                    if ($entity->isFlying() or $entity->getAllowFlight() and $entity->isSurvival()) {

                        $event->cancel();
                        $damager->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $entity->getName() . '§r находится в режиме полета.');
                        return;
                    
                    }
                    
                    if ($damager->isFlying() or $damager->getAllowFlight() and $damager->isSurvival()) {
                        
                        $event->cancel();
                        $damager->sendMessage(Core::getAPI()->getPrefix() . 'Вы находитесь в режиме§b полета§r.');
                        return;

                    }

               
                    if ($entity->isTagged()) {
                    
                        $entity->combatTag();
                    
                    } else {
                    
                        $entity->combatTag();
                        $entity->sendMessage(Core::getAPI()->getPrefix() . 'Вы в режиме §bсражения§r. Если Вы покинете игру, то§b погибните§r.');
                        $entity->sendMessage(Core::getAPI()->getPrefix() . 'Ваш соперник играет с §b' . $damager->getDevice() . '§r.');

                    }

                    if ($damager->isTagged()) {
                    
                        $damager->combatTag();
                    
                    } else {
                        
                        $damager->combatTag();
                        $damager->sendMessage(Core::getAPI()->getPrefix() . 'Вы в режиме §bсражения§r. Если Вы покинете игру, то§b погибните§r.');
                        $damager->sendMessage(Core::getAPI()->getPrefix() . 'Ваш соперник играет с §b' . $entity->getDevice() . '§r.');
                        
                    }

                }
                
            }

        }

        public function handleEntityTeleport(EntityTeleportEvent $event) : void 
        {
            $entity = $event->getEntity();

            if ($entity instanceof BLPlayer) {
                
                if ($entity->isTagged()) {

                    $event->cancel();
                    $entity->sendMessage(Core::getAPI()->getPrefix() . 'Вас кто-то §bпытался §rтелепортировать во время сражения.');
    
                }

            }

        }

    }

?>
