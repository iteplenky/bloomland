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

            $player = $event->getEntity();
            $cause = $player->getLastDamageCause();

            $server = Core::getAPI()->getServer();
            
            $name = $player->getName();

            if ($player instanceof BLPlayer) {

                switch ($cause === null ? EntityDamageEvent::CAUSE_CUSTOM : $cause->getCause()) {

                    case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
                        if ($cause instanceof EntityDamageByEntityEvent) {

                            $event = $cause->getDamager();

                            if ($event instanceof BLPlayer) {

                                $server->broadcastPopup('§fИгрок §b' . $name . ' §fубит игроком §d' . $event->getName() . '§f.');
                                break;

                            } elseif ($event instanceof Living) {

                                $server->broadcastPopup('§fИгрок §b' . $name . ' §fубит игроком §d' . $event->getName() . '§f.');
                                break;

                            } else 
                                $server->broadcastPopup('§fИгрок §b' . $name . ' §fубит стрелами.');
                        }
                        break;

                    case EntityDamageEvent::CAUSE_PROJECTILE:
                        if ($cause instanceof EntityDamageByEntityEvent) {

                            $event = $cause->getDamager();
                            
                            if ($event instanceof BLPlayer) 
                                $server->broadcastPopup('§fИгрок §b' . $name . '§f застрелен игроком §d' . $event->getName() . '§f.');
                            elseif ($event instanceof Living) {
                                $server->broadcastPopup('§fИгрок §b' . $name . '§f убит стрелами.');
                                break;
                            } else
                                $server->broadcastPopup('§fИгрок §b' . $name . '§f убит стрелами.');
                        }
                        break;

                    case EntityDamageEvent::CAUSE_SUICIDE:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fсовершил самоубийство.');
                        break;

                    case EntityDamageEvent::CAUSE_VOID:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fпровалился в пропасть.');
                        break;

                    case EntityDamageEvent::CAUSE_FALL:
                        if ($cause instanceof EntityDamageEvent) {
                            if ($cause->getFinalDamage() > 2) {
                                $server->broadcastPopup('§fИгрок §b' . $name . ' §fумер.');
                                break;
                            }
                        }
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fумер.');
                        break;

                    case EntityDamageEvent::CAUSE_SUFFOCATION:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fпытался пройти сквозь блоки.');
                        break;

                    case EntityDamageEvent::CAUSE_LAVA:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fпринял ванну из лавы.');
                        break;

                    case EntityDamageEvent::CAUSE_FIRE:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fподгорел.');
                        break;

                    case EntityDamageEvent::CAUSE_FIRE_TICK:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fзажарился.');
                        break;

                    case EntityDamageEvent::CAUSE_DROWNING:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fдумал что не глубоко и умер.');
                        break;

                    case EntityDamageEvent::CAUSE_CONTACT:
                        if ($cause instanceof EntityDamageByBlockEvent){
                            if ($cause->getDamager()->getId() === VanillaBlocks::CACTUS())
                                $server->broadcastPopup('§fИгрок §b' . $name . ' §fзадумался когда целовал кактус и умер.');
                        }
                        break;

                    case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
                    case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
                        if ($cause instanceof EntityDamageByEntityEvent) {

                            $event = $cause->getDamager();
                            
                            if ($event instanceof BLPlayer)
                                $server->broadcastPopup('§fИгрок §b' . $name . '§f застрелен взорван игроком §d' . $event->getName() . '§f.');

                            elseif ($event instanceof Living) {
                                $server->broadcastPopup('§fИгрок §b' . $name . ' §fвзорвался.');
                                break;
                            }
                        } else
                            $server->broadcastPopup('§fИгрок §b' . $name . ' §fумер.');
                        break;

                    case EntityDamageEvent::CAUSE_MAGIC:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fубит магией.');
                        break;

                    case EntityDamageEvent::CAUSE_CUSTOM:
                        $server->broadcastPopup('§fИгрок §b' . $name . ' §fумер.');
                        break;
                }

            }

        }

    }

?>
