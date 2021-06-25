<?php 


namespace BloomLand\Crates\entity;

    
    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\resources\LoadResources;

    use BloomLand\Core\utils\API;
    use BloomLand\GenericSound;

    use pocketmine\entity\Skin;
    use pocketmine\entity\Human;

    use pocketmine\math\Vector3;
    use pocketmine\entity\Location;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\entity\EntitySizeInfo;
    use pocketmine\entity\EntityDataHelper;

    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    class AirBalloon extends Human 
    {
        public function onUpdate(int $currentTick) : bool
        { 
            return parent::onUpdate($currentTick);
        }

        public function attack(EntityDamageEvent $source) : void
        {
            $source->cancel();
            
            if ($source instanceof EntityDamageByEntityEvent) {
                
                $player = $source->getDamager();
                
                if ($player instanceof BLPlayer) {
                    
                    // nothing..
                    
                }

            }
            
        }

        public function canBePushed() : bool 
        { 
            return false; 
        }
        
        public function canBeMovedByCurrents() : bool 
        { 
            return false; 
        }
    
        public function getInitialSizeInfo() : EntitySizeInfo 
        {
            return new EntitySizeInfo(0.9, 0.9, 0); /* height, width, eyeHeight */
        }
        
        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);

            $this->setScale(2);

            $this->spawnToAll();
        }

    }

?>
