<?php

namespace BloomLand\Core\entity;

    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use BloomLand\Core\utils\API;
    use BloomLand\Core\resources\LoadResources;
   
    use pocketmine\math\Vector3;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\entity\Skin;
    use pocketmine\entity\Human;
    use pocketmine\entity\Location;
    
    use pocketmine\entity\EntitySizeInfo;
    use pocketmine\entity\EntityDataHelper;

    class Head extends Human
    {
        private $headTimer;

        private $headGeometry = '
        {
            "geometry.player_head": {
                "texturewidth": 64,
                "textureheight": 64,
                "bones":
                    [
                        {
                            "name":"head",
                            "pivot": [0, 24, 0],
                            "cubes":
                            [
                                {
                                    "origin": [-4, 0, -4],
                                    "size": [8, 8, 8],
                                    "uv": [0, 0]
                                }
                            ]
                        }
                    ]
                }
            }';

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);
            
            if (!is_null(self::getCustomSkin())) {
                
                $this->setSkin(self::getCustomSkin());

            }
            
            $this->setNameTagAlwaysVisible();
            
            $this->spawnToAll();
        }

        public function onUpdate(int $currentTick) : bool
        { 
            
            return parent::onUpdate($currentTick);
        }

        public function onClick(BLPlayer $player) : void
        {
            $this->kill();
        }

        public function getHeadGeometry() : string 
        {
            return $this->headGeometry;
        }

        public static function getCustomSkin() : ?Skin
        {
            return new Skin($this->getSkinId(), $this->getSkin()->getSkinData(), '', 'geometry.player_head', $this->getHeadGeometry());
        }
        
        public function getCustomNameTag() : string
        {
            return '§l> опа §f<';
        }
        
        public static function createNBT(Vector3 $pos) : CompoundTag
        {
            $nbt = EntityDataHelper::createBaseNBT($pos->floor()->add(0.5, 0, 0.5), null, 0, 0);
            return $nbt;
        }

        public function attack(EntityDamageEvent $source) : void
        {
            if ($source instanceof EntityDamageByEntityEvent) {
                
                $player = $source->getDamager();
                
                if ($player instanceof BLPlayer) 
                    $this->onClick($source->getDamager());
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
            return new EntitySizeInfo(0.6, 0.5, 0); /* height, width, eyeHeight */
        }

    }

?>
