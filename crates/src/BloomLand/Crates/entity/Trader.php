<?php 


namespace BloomLand\Crates\entity;

    
    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\entity\Skin;
    use pocketmine\entity\Human;

    use pocketmine\math\Vector3;
    use pocketmine\entity\Location;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\item\ItemFactory;

    use pocketmine\entity\EntitySizeInfo;

    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    class Trader extends Human 
    {
        // Предметы по дешевой цене
		private $low_cost = [1, 2, 3, 4, 5, 14, 15, 16, 17, 18, 24, 263, 295];

		// Предметы по средней цене
		private $medium_cost = [35, 41, 42, 45, 81, 130, 152, 157, 158, 159, 388];

		// Предметы по дорогой цене
		private $high_cost = [165, 173, 276, 277, 278, 279, 310, 311, 312, 313, 368];

        public function onClick(BLPlayer $player) : void
        {
            if ($player->isSurvival()) {

                $heldItem = $player->getInventory()->getItemInHand();

                if ($heldItem->getId() != 0) {

                    if (in_array($heldItem->getId(), $this->low_cost)) $booster = mt_rand(2, 4);

                    elseif (in_array($heldItem->getId(), $this->medium_cost)) $booster = mt_rand(3, 7);

                    elseif (in_array($heldItem->getId(), $this->high_cost)) $booster = mt_rand(9, 12);

                    else $booster = mt_rand(1, 3);

                    $money = $heldItem->getCount() * $booster;

                    $player->addMoney($money);

                    $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(0));

                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы продали предмет за §b' . $money . ' §rмонет.');
                    $player->sendTitle('§b§lпродано', '', 10, 15, 10);
                    
                } else $player->sendMessage(Core::getAPI()->getPrefix() . 'Возьмите §bпредмет в руку§r, чтобы продать его.');
                    
            } else $player->sendTitle('§b§lсмените', 'свой игровой режим', 10, 15, 10);
                
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
            return new EntitySizeInfo(1.5, 0.8, 0); /* height, width, eyeHeight */
        }
        
        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($location, $skin, $nbt);

            $this->setScale(1.1);

            $this->setNameTag('§b§lСкупщик');
            $this->setNameTagAlwaysVisible();

            $this->spawnToAll();
        }

    }

?>
