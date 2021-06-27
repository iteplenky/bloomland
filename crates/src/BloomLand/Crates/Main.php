<?php 


namespace BloomLand\Crates;

    
    use BloomLand\Core\Core;

    use BloomLand\Crates\command\CratePlaceCommand;

    use BloomLand\Crates\entity\crate\manager\CrateManager;
    use BloomLand\Crates\entity\crate\PatrickCrate;

    use BloomLand\Crates\entity\EnchantedAsh;
    use BloomLand\Crates\entity\Trader;

    use pocketmine\plugin\PluginBase;
    
    use pocketmine\player\Player;
    use pocketmine\entity\Skin;
    
    use pocketmine\world\World;
    use pocketmine\entity\Human;
    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\entity\EntityFactory;
    use pocketmine\entity\EntityDataHelper;

    class Main extends PluginBase 
    {
        public function onEnable() : void
        {
            $this->initCrates();

            $this->getLogger()->notice('Crates initialized!');

            $this->getServer()->getCommandMap()->register($this->getName(), new CratePlaceCommand($this));
        }

        public function getPrefix() : string
        {
            return Core::getAPI()->getPrefix();
        }

        public function modelsControl(Player $player, $value) : void 
        {
            switch ($value) {
                case 0:
                    $skin = $this->makeSkin('patrick_chest', 'PatrickChest');
                    $entity = new PatrickCrate($player->getLocation(), $skin, CompoundTag::create());
                    break;

                case 1:
                    $skin = $this->makeSkin('enchanted_ash', 'EnchantedAsh');
                    $entity = new EnchantedAsh($player->getLocation(), $skin, CompoundTag::create());
                    break;

                case 2:
                    $skin = $this->makeSkin('trader', 'Trader');
                    $entity = new Trader($player->getLocation(), $skin, CompoundTag::create());
                    break;
            }

            if (is_numeric($value)) {
            
                $entity->spawnToAll();   
           
            }
        }

        private function initCrates() : void
        {
            $factory = EntityFactory::getInstance();

            $factory->register(CrateManager::class, function(World $world, CompoundTag $nbt) : CrateManager {
                return new CrateManager(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['CrateManager']);

            $factory->register(PatrickCrate::class, function(World $world, CompoundTag $nbt) : PatrickCrate {
                return new PatrickCrate(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['PatrickCrate']);

            $factory->register(EnchantedAsh::class, function(World $world, CompoundTag $nbt) : EnchantedAsh {
                return new EnchantedAsh(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['EnchantedAsh']);

            $factory->register(Trader::class, function(World $world, CompoundTag $nbt) : Trader {
                return new Trader(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['Trader']);

        }

        private function makeSkin(string $geometryName, string $folderName) : Skin 
        {
            $path = $this->getFile() . '/resources/' . $folderName . '/';

            $bytes = $this->getBytes($path . $geometryName .'.png', $geometryName);

            return new Skin('name', $bytes, '' ,'geometry.' . $geometryName, file_get_contents($path . $geometryName . '.json'));
        }

        private function getBytes(string $path, string $geometryName) : string
        {
            $img = @imagecreatefrompng($path);

            $bytes = '';

            $L = (int) @getimagesize($path)[0];
            $l = (int) @getimagesize($path)[1];

            for ($y = 0; $y < $l; $y++) {

                for ($x = 0; $x < $L; $x++) {

                    $rgba = @imagecolorat($img, $x, $y);
                    $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                    $r = ($rgba >> 16) & 0xff;
                    $g = ($rgba >> 8) & 0xff;
                    $b = $rgba & 0xff;
                    $bytes .= chr($r) . chr($g) . chr($b) . chr($a);

                }

            }

            @imagedestroy($img);

            return $bytes;
        }

    }

?>
      
