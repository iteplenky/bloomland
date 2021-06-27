<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\event\Listener;

    use pocketmine\event\player\PlayerDeathEvent;

    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByBlockEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\entity\Living;
    use pocketmine\block\VanillaBlocks;

    class PlayerDeath implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handlePlayerDeath(PlayerDeathEvent $event) : void
        {
            $event->setDeathMessage(null);

            $prefix = Core::getAPI()->getPrefix();
            $server = Core::getAPI()->getServer();

            $player = $event->getEntity();
            $cause = $player->getLastDamageCause();
            
            $name = $player->getName();

            if ($player instanceof BLPlayer) {

                switch ($cause === null ? EntityDamageEvent::CAUSE_CUSTOM : $cause->getCause()) {

                    case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                        if ($cause instanceof EntityDamageByEntityEvent) {

                            $event = $cause->getDamager();

                            if ($event instanceof BLPlayer) {

                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rубит игроком §d' . $event->getName() . '§r.');
                                break;

                            } elseif ($event instanceof Living) {

                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rубит игроком §d' . $event->getName() . '§r.');
                                break;

                            } else 
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rубит стрелами.');
                        }
                        break;

                    case EntityDamageEvent::CAUSE_PROJECTILE:
                        if ($cause instanceof EntityDamageByEntityEvent) {

                            $event = $cause->getDamager();
                            
                            if ($event instanceof BLPlayer) 
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . '§r застрелен игроком §d' . $event->getName() . '§r.');
                            elseif ($event instanceof Living) {
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . '§r убит стрелами.');
                                break;
                            } else
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . '§r убит стрелами.');
                        }
                        break;

                    case EntityDamageEvent::CAUSE_SUICIDE:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rсовершил самоубийство.');
                        break;

                    case EntityDamageEvent::CAUSE_VOID:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rпровалился в пропасть.');
                        break;

                    case EntityDamageEvent::CAUSE_FALL:
                        if ($cause instanceof EntityDamageEvent) {
                            if ($cause->getFinalDamage() > 2) {
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rумер.');
                                break;
                            }
                        }
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rумер.');
                        break;

                    case EntityDamageEvent::CAUSE_SUFFOCATION:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rпытался пройти сквозь блоки.');
                        break;

                    case EntityDamageEvent::CAUSE_LAVA:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rпринял ванну из лавы.');
                        break;

                    case EntityDamageEvent::CAUSE_FIRE:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rподгорел.');
                        break;

                    case EntityDamageEvent::CAUSE_FIRE_TICK:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rзажарился.');
                        break;

                    case EntityDamageEvent::CAUSE_DROWNING:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rдумал что не глубоко и умер.');
                        break;

                    case EntityDamageEvent::CAUSE_CONTACT:
                        if ($cause instanceof EntityDamageByBlockEvent){
                            if ($cause->getDamager()->getId() === VanillaBlocks::CACTUS()->getId())
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rзадумался когда целовал кактус и умер.');
                        }
                        break;

                    case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                    case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
                        if ($cause instanceof EntityDamageByEntityEvent) {

                            $event = $cause->getDamager();
                            
                            if ($event instanceof BLPlayer)
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . '§r взорван игроком §d' . $event->getName() . '§r.');

                            elseif ($event instanceof Living) {
                                $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rвзорвался.');
                                break;
                            }
                        } else
                            $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rумер.');
                        break;

                    case EntityDamageEvent::CAUSE_MAGIC:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rубит магией.');
                        break;

                    case EntityDamageEvent::CAUSE_CUSTOM:
                        $server->broadcastMessage($prefix . ' §rИгрок §b' . $name . ' §rумер.');
                        break;
                }

            }

        }

    }

?>
