<?php 


namespace BloomLand\Crates;

    
    use BloomLand\Core\Core;

    use pocketmine\player\Player;

    use pocketmine\plugin\PluginBase;

    use BloomLand\Crates\command\CratePlaceCommand;

    use BloomLand\Crates\crate\manager\CrateManager;

    use BloomLand\Crates\crate\PatrickCrate;
    use BloomLand\Crates\crate\AirBalloon;
    use BloomLand\Crates\crate\EnchantedAsh;

    use pocketmine\nbt\tag\CompoundTag;

    use pocketmine\entity\Human;
    use pocketmine\entity\EntityFactory;
    use pocketmine\entity\EntityDataHelper;
    use pocketmine\world\World;

    class Main extends PluginBase 
    {
        private static $api;
        
        public static function getPrefix() : string
        {
            return Core::getAPI()->getPrefix();
        }

        public function onLoad() : void 
        {
            self::$api = $this;
        }

        public function onEnable() : void
        {
            self::initCrates();

            $this->getLogger()->notice('Crates initialized!');

            $this->getServer()->getCommandMap()->registerAll($this->getName(), [new CratePlaceCommand($this)]);
        }

        public function modelsControl(Player $player, $value) : void 
        {
            switch ($value) {
                case 0:
                    $location = $player->getLocation();
                    $entity = new PatrickCrate($location, $player->getSkin(), CompoundTag::create());
                    $entity->spawnToAll();
                    break;

                case 1:
                    $location = $player->getLocation();
                    $entity = new AirBalloon($location, $player->getSkin(), CompoundTag::create());
                    $entity->spawnToAll();
                    break;

                case 2:
                    $location = $player->getLocation();
                    $entity = new EnchantedAsh($location, $player->getSkin(), CompoundTag::create());
                    $entity->spawnToAll();
                    break;
                
                default:
                    $player->sendMessage(' ');
                    break;
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

            $factory->register(AirBalloon::class, function(World $world, CompoundTag $nbt) : AirBalloon {
                return new AirBalloon(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['AirBalloon']);

            $factory->register(EnchantedAsh::class, function(World $world, CompoundTag $nbt) : EnchantedAsh {
                return new EnchantedAsh(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
            }, ['EnchantedAsh']);

        }

        public function onDisable() : void
        {
        	$world = $this->getServer()->getWorldManager()->getWorldByName('world');
            
        }

        public function makeSkin(string $skinFileName, string $geometryFileName, string $path, string $geometryName) : Skin 
        {
            $img = @imagecreatefrompng($path . $skinFileName . ".png");
            $size = (int) @getimagesize($path . $skinFileName . ".png")[1];
    
            $bytes = self::getBytes($size, $img);
    
            @imagedestroy($img);
    
            return new Skin("name", $bytes, "" ,"geometry.". $geometryName, file_get_contents($path. $geometryFileName. ".json"));
        }

        public function getBytes(int $size, $img) : string 
        {
            $bytes = "";
    
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
