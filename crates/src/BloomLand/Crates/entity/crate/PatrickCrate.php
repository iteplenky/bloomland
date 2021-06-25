<?php 


namespace BloomLand\Crates\entity\crate;

    
    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\resources\LoadResources;
    use BloomLand\Crates\animation\PatrickCrateAnimationTask;

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
    use pocketmine\network\mcpe\protocol\types\ParticleIds;
    use pocketmine\world\particle\FlameParticle;
    use pocketmine\color\Color;
    use pocketmine\world\particle\DustParticle;
    use pocketmine\network\mcpe\protocol\LevelEventPacket;
    use pocketmine\world\particle\Particle;

    class PatrickCrate extends Human 
    {
        private $particleUpdate = 0; private $particleTimer = 15;
        private $animationUpdate = 0; private $animationTime = 9;

        private $cooldownTime = 30; //60 * 60 * 18

        public static $animation = [];

        private $crateCooldown;
        
        public function onClick(BLPlayer $player) : void
        {
            if (!$this->isActivate()) {

                if ($this->checkCooldown($player)) {

                    new PatrickCrateAnimationTask($this, $player);

                    self::$animation[] = $this->getId();

                } else {

                    API::sendErrorPackets($player);

                    $duration = ($this->crateCooldown[$player->getLowerCaseName()] - API::getMicroTime()) / 1000;

                    $player->sendTitle('§b§lлимит', 'следующий раз через: §b' . API::getTimeFormat($duration), 5, 30, 10);

                }

            } else {

                API::sendErrorPackets($player);

            }

        }

        public function onUpdate(int $currentTick) : bool
        { 
            if (API::getMicroTime() >= $this->particleUpdate and !$this->isActivate()) {

                $this->particleUpdate = API::getMicroTime() + $this->particleTimer * 1000;

                $vector = new Vector3($this->location->x , $this->location->y + 0.2, $this->location->z);

                $world = $this->getLocation()->getWorld();

                // $pk = new LevelEventPacket();
                // $pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | (ParticleIds::SPARKLER & 0xFFF);
                // $pk->data = (new Color(0, 255, 0))->toARGB();

                // Core::getAPI()->getServer()->broadcastPackets(Core::getAPI()->getServer()->getOnlinePlayers(), [$pk]);

                API::sendParticlePacket($this, $vector, 'bloomland:crate_marker');

            }

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
                    
                    } elseif ($player->getInventory()->getItemInHand()->getId() == 2) {

                        API::sendDialog($player, '', '§f§lМэр', '§7Голосуйте за меня на §bвыборах§r!');

                    } else
                        $this->onClick($source->getDamager());

                }

            }
            
        }

        public function isActivate() : bool
        {
            return in_array($this->getId(), self::$animation);
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
            return new Skin('MoneyCrate', LoadResources::PNGtoBYTES('patrick_chest'), '', 'geometry.patrick_chest', LoadResources::getGeometry('patrick_chest'));
        }

        public function checkCooldown($player) : bool 
        {
            if (isset($this->crateCooldown[$player->getLowerCaseName()])) {

                if (API::getMicroTime() >= $this->crateCooldown[$player->getLowerCaseName()]) {

                    $this->crateCooldown[$player->getLowerCaseName()] = API::getMicroTime() + $this->cooldownTime * 1000;
                    return true;

                } else {

                    return false;

                }

            } else {

                $this->crateCooldown[$player->getLowerCaseName()] = API::getMicroTime() + $this->cooldownTime * 1000;
                return true;

            }

        }

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);

            if (!is_null(self::getCustomSkin())) {

                $this->setSkin(self::getCustomSkin());

            }

            $this->setScale(1.1);
            // $this->setNameTagAlwaysVisible();

            $this->spawnToAll();
        }

    }

?>
