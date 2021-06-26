<?php

namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
    
    use pocketmine\world\WorldManager;
    use pocketmine\entity\projectile\Arrow;
    use BloomLand\Core\BLPlayer;
    use pocketmine\player\Player;
    use pocketmine\entity\Living;
    use pocketmine\entity\object\{ItemEntity, Painting};
    
    use pocketmine\scheduler\Task;

    class CleanerTask extends Task
    {
        public function __construct(WorldManager $manager)
        {
            $this->manager = $manager;
        }

        public function onRun(): void
        { 
            $count = 0;
            
            foreach ($this->manager->getWorlds() as $level) {

                foreach ($level->getEntities() as $entity) {
                    
                    if($entity instanceof ItemEntity or $entity instanceof Painting) {

                        $count++;

                        if ($count == 50) {

                            Core::getAPI()->getScheduler()->scheduleDelayedTask(new CompleteCleanerTask( $this->manager), 10 * 20); 
                            Core::getAPI()->getServer()->broadcastMessage(Core::getPrefix() . ' Игровой мир скоро будет §bочищен §rот случайных предметов на земле.');

                        } 

                    }

                }

            }

        }

    }

    class CompleteCleanerTask extends Task
    {
        public function __construct(WorldManager $manager)
        {
            $this->manager = $manager;
        }

        public function onRun(): void
        {
            $count = 0;

            foreach ($this->manager->getWorlds() as $level) {

                foreach ($level->getEntities() as $entity) {
                
                    if ($entity instanceof ItemEntity or $entity instanceof Painting) {

                        $count++;
                        $entity->close();
                        
                        
                    }
                    
                }
                
                Core::getAPI()->getServer()->broadcastMessage(Core::getPrefix() . 'Игровой мир очищен от §b' . $count . '§r случайных предметов на земле.');

            }

        }

    }

?>