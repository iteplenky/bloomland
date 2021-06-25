<?php 


namespace BloomLand\Crates;

    
    use BloomLand\Core\Core;

    use BloomLand\Crates\command\CratePlaceCommand;

    use BloomLand\Crates\crate\manager\CrateManager;

    use BloomLand\Crates\crate\PatrickCrate;
    use BloomLand\Crates\crate\AirBalloon;
    use BloomLand\Crates\crate\EnchantedAsh;

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
            $path = $this->getFile() . '/resources/';

            switch ($value) {
                case 0:
                     $skin = $this->makeSkin(
                        'patrick_chest', 
                        'patrick_chest', 
                        $path, 
                        'patrick_chest', 
                        'PatrickChest'
                    );
                    $entity = new PatrickCrate($player->getLocation(), $skin, CompoundTag::create());
                    break;

                case 1:
                     $skin = $this->makeSkin(
                        'air_balloon', 
                        'air_balloon', 
                        $path, 
                        'air_balloon', 
                        'AirBalloon'
                    );
                    $entity = new AirBalloon($player->getLocation(), $skin, CompoundTag::create());
                    break;

                case 2:
                    $skin = $this->makeSkin(
                        'enchanted_ash', 
                        'enchanted_ash', 
                        $path, 
                        'enchanted_ash', 
                        'EnchantedAsh'
                    );
                    $entity = new EnchantedAsh($player->getLocation(), $skin, CompoundTag::create());
                    break;
            }

            $entity->spawnToAll();
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

            $factory->register(AirBalloon::class, function(World $world, CompoundTag $nbt) : AirBalloon {
                return new AirBalloon(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['AirBalloon']);

            $factory->register(EnchantedAsh::class, function(World $world, CompoundTag $nbt) : EnchantedAsh {
                return new EnchantedAsh(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['EnchantedAsh']);

        }

        private function makeSkin(string $skinFileName, string $geometryFileName, string $path, string $geometryName, string $folderName) : Skin 
        {
            $path = $path . $folderName . '/';

            $img = @imagecreatefrompng($path . $skinFileName . '.png');
            $size = (int) @getimagesize($path . $skinFileName . '.png')[1];
    
            $bytes = $this->getBytes($size, $img);
    
            @imagedestroy($img);
    
            return new Skin($folderName, $bytes, '' ,'geometry.' . $geometryName, file_get_contents($path . $geometryFileName . '.json'));
        }

        private function getBytes(int $size, $img) : string 
        {
            $bytes = '';
    
            for ($y = 0; $y < $size; $y ++) {
        
                for ($x = 0; $x < 64; $x ++) {
                    $color = imagecolorat($img, $x, $y);
                    $a = ((~((int) ($color >> 24))) << 1) & 0xff;
                    $r = ($color >> 16) & 0xff;
                    $g = ($color >> 8) & 0xff;
                    $b = $color & 0xff;
                    $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
                }
        
            }
    
            return $bytes;
        }

    }

?>
