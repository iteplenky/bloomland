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
    
    use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
    use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
    use pocketmine\network\mcpe\protocol\PlaySoundPacket;

    class Yoda extends Human
    {
        private $boosterCooldown;

        private $particleUpdate = 0; private $particleTimer = 15;

        private $animationUpdate = 0; private $animationTime = 20;

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);
            
            if (!is_null(self::getCustomSkin())) {
                
                $this->setSkin(self::getCustomSkin());

            }

            $this->setScale(1.10);
            $this->setNameTag($this->getCustomNameTag());
            $this->setNameTagAlwaysVisible();
            
            $this->spawnToAll();
        }

        public function onUpdate(int $currentTick): bool
        { 
            if (API::getMicroTime() >= $this->particleUpdate) {

                $this->particleUpdate = API::getMicroTime() + $this->particleTimer * 1000;

                for ($i = 0; $i < 7; $i++) { 

                    $pk = new SpawnParticleEffectPacket();

                    $vector = new Vector3($this->location->x + mt_rand(-2.5, 2.5), $this->location->y + mt_rand(0, 2.5), $this->location->z + mt_rand(-2.5, 2.5));

                    $pk->position = $vector;
                    $pk->particleName = 'bloomland:crimson_spore';
                    Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

                }

            }

            if (API::getMicroTime() >= $this->animationUpdate) {

                $this->animationUpdate = API::getMicroTime() + $this->animationTime * 1000;

                $pk = AnimateEntityPacket::create('animation.unknown.new', '', '', '', 0, [$this->getId()]);

                Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

            } 
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
                
            } else {

                $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5, 4, false));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5, 4, false)); 

                $pk = new PlaySoundPacket();
				$pk->soundName = 'sfx.error';
				$pk->x = $player->getLocation()->x;
				$pk->y = $player->getLocation()->y;
				$pk->z = $player->getLocation()->z;
				$pk->volume = 100;
				$pk->pitch = 1;

				$player->getNetworkSession()->sendDataPacket($pk);

            }
            
        }

        public static function getCustomSkin(): ?Skin
        {
            return new Skin('Yoda', LoadResources::PNGtoBYTES('baby_yoda_carrier'), '', 'geometry.unknown', LoadResources::getGeometry('yoda.geo'));
        }
        
        public function getCustomNameTag(): string
        {
            return '§l> §bГрогу §f<';
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
            return new EntitySizeInfo(1, 0.5, 0); /* height, width, eyeHeight */
        }

    }

?>
