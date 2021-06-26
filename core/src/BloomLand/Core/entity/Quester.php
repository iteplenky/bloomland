<?php


namespace BloomLand\Core\entity;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use BloomLand\Core\resources\LoadResources;
    // use BloomLand\Core\task\questers\MoneyquesterAnimation;
    
    use BloomLand\Core\utils\API;
    
    use pocketmine\entity\Skin;
    use pocketmine\entity\Human;
    
    use pocketmine\math\Vector3;
    use pocketmine\entity\Location;
    use pocketmine\nbt\tag\CompoundTag;
    
    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\entity\EntitySizeInfo;
    use pocketmine\entity\EntityDataHelper;

    class Quester extends Human
    {
        private $particleUpdate = 0; private $particleTimer = 15;
        private $animationUpdate = 0; private $animationTime = 9;

        private $cooldownTime = 1;

        private $questerCooldown;

        public function onClick(BLPlayer $player) : void
        {

            if ($this->checkCooldown($player)) {

                API::playSoundPacket($player, 'mono.voices.gorf.gorf');

            } else {

                API::sendErrorPackets($player);

                $duration = ($this->questerCooldown[$player->getLowerCaseName()] - API::getMicroTime()) / 1000;

            }            

        }

        public function onUpdate(int $currentTick) : bool
        { 
            // if (API::getMicroTime() >= $this->particleUpdate and !$this->isActivate()) {

                // $this->particleUpdate = API::getMicroTime() + $this->particleTimer * 1000;

                // $vector = new Vector3($this->location->x , $this->location->y, $this->location->z);

                // API::sendParticlePacket($this, $vector, 'bloomland:quester_marker');

            // }

            // todo: simple animation for waiting..

            // if (API::getMicroTime() >= $this->animationUpdate and !$this->isActivate()) {

            //     $this->animationUpdate = API::getMicroTime() + $this->animationTime * 1000;

            //     $pk = AnimateEntityPacket::create('animation.patrick_chest.new', '', '', '', 0, [$this->getId()]);

            //     Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

            // } 

            return parent::onUpdate($currentTick);
        }

        public function attack(EntityDamageEvent $source) : void
        {
            $source->cancel();
            
            if ($source instanceof EntityDamageByEntityEvent) {
                
                $player = $source->getDamager();
                
                if ($player instanceof BLPlayer) {

                    if ($player->getInventory()->getItemInHand()->getId() == 1) {
                    
                        $this->location->yaw += 5;
                    
                    } 
                    elseif ($player->getInventory()->getItemInHand()->getId() == 3) {
                    
                        $this->kill();
                    
                    } else
                        $this->onClick($source->getDamager());

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
            return new EntitySizeInfo(0.9, 0.8, 0); /* height, width, eyeHeight */
        }

        public static function createNBT(Vector3 $pos) : CompoundTag
        {
            $nbt = EntityDataHelper::createBaseNBT($pos->floor()->add(0.5, 0, 0.5), null, 0, 0);
            return $nbt;
        }
        
        public static function getCustomSkin() : ?Skin
        {
            return new Skin('QuestersSkin', LoadResources::PNGtoBYTES('gorf_base'), '', 'geometry.gorf_base', LoadResources::getGeometry('gorf_base'));
        }

        public function checkCooldown($player) : bool 
        {
            if (isset($this->questerCooldown[$player->getLowerCaseName()])) {

                if (API::getMicroTime() >= $this->questerCooldown[$player->getLowerCaseName()]) {

                    $this->questerCooldown[$player->getLowerCaseName()] = API::getMicroTime() + $this->cooldownTime * 1000;
                    return true;

                } else {

                    return false;

                }

            } else {

                $this->questerCooldown[$player->getLowerCaseName()] = API::getMicroTime() + $this->cooldownTime * 1000;
                return true;

            }

        }

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);

            if (!is_null(self::getCustomSkin())) {

                $this->setSkin(self::getCustomSkin());

            }

            $this->setScale(1.0);
            // $this->setNameTagAlwaysVisible();

            $this->spawnToAll();
        }

    }
?>