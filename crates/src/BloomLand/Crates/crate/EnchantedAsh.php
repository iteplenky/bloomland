<?php 


namespace BloomLand\Crates\crate;

    
    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use BloomLand\Core\resources\LoadResources;

    use pocketmine\entity\Skin;
    use pocketmine\entity\Human;

    use pocketmine\math\Vector3;
    use pocketmine\entity\Location;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\item\Durable;

    use pocketmine\entity\EntitySizeInfo;
    use pocketmine\entity\EntityDataHelper;

    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\network\mcpe\protocol\LevelEventPacket;

    use pocketmine\item\enchantment\EnchantmentInstance;
    use pocketmine\item\enchantment\VanillaEnchantments;

    class EnchantedAsh extends Human 
    {
        private $enchants = [
            'BLAST_PROTECTION',
            'EFFICIENCY',
            'FEATHER_FALLING',
            'FIRE_ASPECT',
            'FIRE_PROTECTION',
            'FLAME',
            'INFINITY',
            'KNOCKBACK',
            'MENDING',
            'POWER',
            'PROJECTILE_PROTECTION',
            'PROTECTION',
            'PUNCH',
            'RESPIRATION',
            'SHARPNESS',
            'SILK_TOUCH',
            'UNBREAKING',
            'VANISHING'
        ];

        private $levels = [1, 2, 3, 4, 5];

        public function onClick(BLPlayer $player) : void
        {
            $balance = $player->getMoney();
            
            if ($balance >= 500) {

                $heldItem = $player->getInventory()->getItemInHand();
                $item = $heldItem->getId();

                if ($item != 0) {

                    if ($heldItem instanceof Durable) {

                        $player->removeMoney(500);

                        $enchant = array_rand($this->enchants, 2);
                        $level = array_rand($this->levels, 2);

                        $heldItem->addEnchantment(new EnchantmentInstance(
                            VanillaEnchantments::fromString($this->enchants[$enchant[0]]), 
                            $this->levels[$level[0]]
                        ));

                        $player->getInventory()->setItemInHand($heldItem);

                        $player->sendTitle('§l§dзачаровано', '', 10, 20, 5);

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Предмет в руке §cзачарован§r на §b' . 
                        $this->enchants[$enchant[0]] . ' §3' . $this->levels[$level[0]] . '§r уровень');
                        
                        $pk = new LevelEventPacket();
                        $pk->evid = LevelEventPacket::EVENT_SOUND_ORB;
                        $pk->position = $player->getPosition()->asVector3();
                        $pk->data = 1;
                        $player->getNetworkSession()->sendDataPacket($pk);
                        
                    } else {

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Этот предмет §cнельзя§r зачаровать');
                        
                    }

                } else {

                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Выберите предмет для зачарования');
                    
                }

            } else {

                $player->sendMessage(Core::getAPI()->getPrefix() . 'У Вас недостаточно монет. Вам не хватает: §c' . 500 - $balance . '§r монет');
                
            }

        }

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
            return new EntitySizeInfo(0.9, 0.9, 0); /* height, width, eyeHeight */
        }
        
        public static function getCustomSkin() : ?Skin
        {
            return new Skin('EnchantedAsh', LoadResources::PNGtoBYTES('enchanted_ash_tray'), '', 'geometry.enchanted_ash', LoadResources::getGeometry('enchanted_ash_tray'));
        }

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);

            if (!is_null(self::getCustomSkin())) {

                $this->setSkin(self::getCustomSkin());

            }

            $this->setScale(1.1);

            $this->setNameTag('Стоимость зачарования - §e500§f монет.');
            $this->setNameTagAlwaysVisible();

            $this->spawnToAll();
        }

    }

?>
