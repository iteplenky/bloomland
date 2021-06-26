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

    use pocketmine\world\particle\InstantEnchantParticle;

    use pocketmine\network\mcpe\protocol\LevelEventPacket;
    
    use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
    use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
    use pocketmine\network\mcpe\protocol\PlaySoundPacket;
    use pocketmine\world\particle\FlameParticle;

    class Booster extends Human
    {
        private $boosterCooldown;

        private $particleUpdate = 0; private $particleTimer = 2;
        
        private $animationUpdate = 0; private $animationTime = 6.3;

        public const DEG_2_RAD = 0.087266462599716;

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

                for ($i = 0; $i < 5; $i++) { 

                    $pk = new SpawnParticleEffectPacket();

                    $vector = new Vector3($this->location->x + mt_rand(-2.5, 2.5), $this->location->y + mt_rand(0, 2.5), $this->location->z + mt_rand(-2.5, 2.5));

                    $pk->position = $vector;
                    $pk->particleName = 'bloomland:booster_clock';
                    Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

                }

            } else {

                $vector = new Vector3($this->location->x + mt_rand(-1.5, 1.5), $this->location->y + mt_rand(0, 1.5), $this->location->z + mt_rand(-1.5, 1.5));
                $this->location->getWorld()->addParticle($vector, new InstantEnchantParticle(new Color(224, 176, 0)));
                

            }

            // if (API::getMicroTime() >= $this->animationUpdate) {

            //     $this->animationUpdate = API::getMicroTime() + $this->animationTime * 1000;

            //     $pk = AnimateEntityPacket::create('animation.booster.new', '', '', '', 0, [$this->getId()]);

            //     Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

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

                $y = $this->getPosition()->y + $this->getEyeHeight() + 1;

                $t = 0.25; //lower radius
                $cos = cos(self::DEG_2_RAD);
                $sin = sin(self::DEG_2_RAD);
                for($yaw = 0, $cy = $y; $cy < $y + 2; $yaw += (M_PI * 2) / 25, $cy += 0.02, $t += 0.01){
                    $diffx = -sin($yaw) * $t;
                    $diffz = cos($yaw) * $t;
                    $rx = $diffx * $cos + $diffz * $sin;
                    $rz = -$diffx * $sin + $diffz * $cos;
                    $this->getWorld()->addParticle(new Vector3($this->getPosition()->x + $rx, $cy, $this->getPosition()->z + $rz), new FlameParticle());
                }

                // $particle = new GenericParticle(ParticleIds::END_ROD);
                 
                // for($i = 0; $i < 12; $i++){
                //     $degRad = deg2rad(30 * $i);
                //     $this->getPosition()->getWorld()->addParticle($this->getPosition()->add(0.35 * cos($degRad), 2, 0.35 * sin($degRad)), $particle);
        
                //     if($i < 5){
                //         $this->getPosition()->getWorld()->addParticle($this->getPosition()->add((lcg_value() - 0.5) / 10, 0.46, (lcg_value() - 0.5) / 10 + 2), $particle);
                //     }
                // }
    
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 60 * 20, 1));

                $player->sendTitle('§l§bБУСТ', '', 10, 20, 5);
                
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
            return new Skin('Booster', LoadResources::PNGtoBYTES('booster'), '', 'geometry.booster', LoadResources::getGeometry('booster'));
        }
        
        public function getCustomNameTag(): string
        {
            return '§l> §bБУСТЕР §f<';
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
            return new EntitySizeInfo(1.2, 0.5, 0); /* height, width, eyeHeight */
        }

    }

?>
