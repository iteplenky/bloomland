<?php 


namespace BloomLand\Crates\crate;

    
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

        public static function createNBT(Vector3 $pos) : CompoundTag
        {
            $nbt = EntityDataHelper::createBaseNBT($pos->floor()->add(0.5, 0, 0.5), null, 0, 0);
            return $nbt;
        }
        
        public static function getCustomSkin() : ?Skin
        {
            return new Skin('AirBalloon', LoadResources::PNGtoBYTES('air_balloon'), '', 'geometry.unknown', LoadResources::getGeometry('air_balloon'));
        }

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);

            if (!is_null(self::getCustomSkin())) {

                $this->setSkin(self::getCustomSkin());

            }

            $this->setScale(1.1);

            $this->spawnToAll();
        }

    }

?>
