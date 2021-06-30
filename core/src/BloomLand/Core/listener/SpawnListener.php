<?php


namespace BloomLand\Core\listener;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\utils\API;

    use pocketmine\event\Listener;

    use pocketmine\event\entity\EntityDamageEvent;

    use pocketmine\item\ItemFactory;
    use pocketmine\block\BlockFactory;
    use pocketmine\math\Vector3;

    use pocketmine\event\block\BlockBreakEvent;

    use pocketmine\event\player as event;

    class SpawnListener implements Listener 
    {
        public function __construct()
        {
            Core::getAPI()->getServer()->getPluginManager()->registerEvents($this, Core::getAPI());
        }

        public function handlePlayerExhaust(event\PlayerExhaustEvent $event) : void 
        {
            $player = $event->getPlayer();

            if ($player instanceof BLPlayer) {
                
                if ($this->isInside($player->getLocation()->asVector3())) {

                    $event->cancel();

                }
            
            }

        }

        public function handleDamage(EntityDamageEvent $event) : void
        {
            $player = $event->getEntity();
            
            if ($player instanceof BLPlayer) {
                
                if ($this->isInside($player->getLocation()->asVector3())) {

                    $event->cancel();

                }

            } else {
                
            }
            
        }

        public function onBlockBreak(BlockBreakEvent $event) : void
        {
            $player = $event->getPlayer();
            $block = $event->getBlock();

            $name = $player->getLowerCaseName();

            if ($block->getId() == 59) {

                if ($this->isInside($block->getPos()->asVector3())) {

                    if ($player->isCreative()) {

                        $player->sendTitle('§bсмените', 'режим игры', 15, 20, 10);
                        $event->cancel();
                        return;

                    } else {

                        $event->cancel();

                        if (isset($this->wheat[$name])) {

                            if ($this->wheat[$name] >= 10) {

                                $player->sendTitle('§l§bпшеница', '§fвы набрали §bмаксимум');
                                return;

                            }

                            $this->wheat[$name]++;
                            $player->sendTitle('§l§bпшеница', '§fвы набрали §b' . $this->wheat[$name] . '§7/10');

                            $item = ItemFactory::getInstance()->get(296, 0, 1);
                            
                            if (!empty($player->getInventory()->addItem($item))) {

                                $player->sendTitle('§l§bпшеница', '§fвы уронили пшеницу §b' . $this->wheat[$name] . '§7/10');
        
                                $player->getWorld()->dropItem($player->getEyePos(), $item);
                    
                            } else {

                                $player->getInventory()->addItem($item);

                            }

                            $this->destroyWheat($event, $block, $player);

                        } else {

                            $this->wheat[$name] = 1;
                            $player->sendTitle('§l§bпшеница', '§fвы набрали §b' . $this->wheat[$name] . '§7/10');

                            $this->destroyWheat($event, $block, $player);

                            $item = ItemFactory::getInstance()->get(296, 0, 1);
                            
                            if (!empty($player->getInventory()->addItem($item))) {

                                $player->sendTitle('§l§bпшеница', '§fвы уронили пшеницу §b' . $this->wheat[$name] . '§7/10');
        
                                $player->getWorld()->dropItem($player->getEyePos(), $item);
                    
                            } else {

                                $player->getInventory()->addItem($item);

                            }

                        }

                    }

                }

            }

        }

        public function destroyWheat($event, $block, $player) : void 
        {
            $item = $event->getItem();
            $item->onDestroyBlock($block);
            $player->getInventory()->setItemInHand($item);

            if (mt_rand(0, 25) < 5) {
                $player->getXpManager()->addXp(1);
            }

            $world = Core::getAPI()->getServer()->getWorldManager()->getDefaultWorld();

            if (!Core::$WheatTime->exists($block->getPos()->x .':'. $block->getPos()->y .':'. $block->getPos()->z)) {
                $id = $block->getId();
                
                Core::$WheatTime->set($block->getPos()->x .':'. $block->getPos()->y .':'. $block->getPos()->z . ':', [Core::$config['regenerate'][$id], $id]);
            }  
            
            $world = Core::getAPI()->getServer()->getWorldManager()->getDefaultWorld();
            $world->setBlock($block->getPos(), BlockFactory::getInstance()->get(0, 0), false);
        }

        public function isInside(Vector3 $player): bool
        {
            $config = Core::getAPI()->getConfig();

            $pos1 = API::unpackRawVector($config->get('spawnPos1'));
            $pos2 = API::unpackRawVector($config->get('spawnPos2'));
            
            $min = [min($pos1->x, $pos2->x), min($pos1->y, $pos2->y), min($pos1->z, $pos2->z)];
			$max = [max($pos1->x, $pos2->x), max($pos1->y, $pos2->y), max($pos1->z, $pos2->z)];

            return $player->x >= $min[0] && $player->x <= $max[0] && $player->y >= $min[1] && $player->y <= $max[1] && $player->z >= $min[2] && $player->z <= $max[2];

        }
    }

?>
