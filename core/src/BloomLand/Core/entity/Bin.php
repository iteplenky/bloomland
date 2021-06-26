<?php

namespace BloomLand\Core\entity;

    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use BloomLand\Core\utils\API;
    use BloomLand\Core\resources\LoadResources;
   
    use pocketmine\math\Vector3;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\color\Color;
    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\entity\Skin;
    use pocketmine\entity\Human;
    use pocketmine\entity\Location;
    
    use pocketmine\entity\effect\EffectInstance;
    use pocketmine\entity\effect\VanillaEffects;
    
    use pocketmine\entity\EntitySizeInfo;
    use pocketmine\entity\EntityDataHelper;

    use pocketmine\network\mcpe\protocol\LevelEventPacket;
    use pocketmine\network\mcpe\protocol\MovePlayerPacket;

    use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
    use pocketmine\network\mcpe\protocol\AnimateEntityPacket;

    class Bin extends Human
    {
        private $boosterCooldown;

        private $particleUpdate = 0; private $particleTimer = 2;
        
        private $animationUpdate = 0; private $animationTime = 6.3;

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);
            
            if (!is_null(self::getCustomSkin())) {
                
                $this->setSkin(self::getCustomSkin());

            }

            $this->setScale(1.10);

            $this->spawnToAll();
        }

        public function onUpdate(int $currentTick): bool
        { 
            // foreach (Core::getAPI()->getServer()->getOnlinePlayers() as $player) {

            //     $distance = $player->getPosition()->distance($this->getPosition());

            //     $pk = new MovePlayerPacket();
            //     $pk->entityRuntimeId = $this->id;
            //     $pk->position = $this->getPosition();
            //     $pk->yaw = 0;
            //     $pk->headYaw = 0;

            //     if ($distance <= 8) {
            //         $this->setNameTag('§dМусорное ведро');
            //         $this->setNameTagAlwaysVisible(true);
            //     }
                
            //     else {
            //         $this->setNameTag('');
            //         $this->setNameTagAlwaysVisible(false);
            //     }                

            //     $player->getNetworkSession()->sendDataPacket($pk);

            // }
            return parent::onUpdate($currentTick);
        }

        public function onClick(BLPlayer $player): void
        {
            if ($this->checkCooldown($player)) {

                $pk = new LevelEventPacket();
                $pk->evid = LevelEventPacket::EVENT_SOUND_ORB;
                $pk->position = $player->getPosition()->asVector3();
                $pk->data = 1;
                $player->getNetworkSession()->sendDataPacket($pk);

                $player->sendMessage('ну да открывается ведро');
                
            } else {

                $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5, 4));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5, 4)); 

            }
            
        }

        public static function getCustomSkin(): ?Skin
        {
            return new Skin('Bin', LoadResources::PNGtoBYTES('bin'), '', 'geometry.bin', LoadResources::getGeometry('bin'));
        }
        
        public static function createNBT(Vector3 $pos): CompoundTag
        {
            $nbt = EntityDataHelper::createBaseNBT($pos->floor()->add(0.5, 0, 0.5), null, 0, 0);
            return $nbt;
        }

        public function attack(EntityDamageEvent $source): void
        {
            if ($source instanceof EntityDamageByEntityEvent) {
                
                $player = $source->getDamager();
                
                if ($player instanceof BLPlayer) 
                if ($player->getInventory()->getItemInHand()->getId() == 1) {
                    $this->kill();
                }else
                    $this->onClick($source->getDamager());
            }
            
        }

        public function checkCooldown($player): bool 
        {
            $cooldownTime = 5;

            if (isset($this->boostedCooldown[$player->getLowerCaseName()])) {

                if (API::getMicroTime() >= $this->boostedCooldown[$player->getLowerCaseName()]) {

                    $this->boostedCooldown[$player->getLowerCaseName()] = API::getMicroTime() + $cooldownTime * 1000;
                    return true;

                } else {

                    return false;

                }

            } else {

                $this->boostedCooldown[$player->getLowerCaseName()] = API::getMicroTime() + $cooldownTime * 1000;
                return true;
            }

        }

        public function canBePushed(): bool 
        { 
            return false; 
        }
        
        public function canBeMovedByCurrents(): bool 
        { 
            return false; 
        }
    
        public function getInitialSizeInfo(): EntitySizeInfo 
        {
            return new EntitySizeInfo(1.2, 0.7, 0); /* height, width, eyeHeight */
        }

    }

?>
