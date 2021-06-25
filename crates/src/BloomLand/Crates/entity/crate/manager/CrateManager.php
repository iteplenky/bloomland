<?php


namespace BloomLand\Crates\entity\crate\manager;

    
    use pocketmine\entity\Human;
    use pocketmine\entity\Living;
    
    use pocketmine\entity\Location;
    use pocketmine\entity\Skin;

    class CrateManager extends Human
    {

        public function __construct(Location $world, Skin $skin, ?CompoundTag $nbt = null)
        {
            parent::__construct($world, $skin, $nbt);

            $this->spawnToAll();
        }

    }

?>
