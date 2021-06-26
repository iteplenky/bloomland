<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use Exception;

    class SkinCommand extends Command
    {
        // https://github.com/pmmp/PocketMine-MP/blob/a19143cae76ad55f1bdc2f39ad007b1fc170980b/src/pocketmine/entity/Skin.php#L33-L37

        public const ACCEPTED_SKIN_SIZES = [
            64 * 32 * 4,
            64 * 64 * 4,
            128 * 128 * 4
        ];
    
        public const SKIN_WIDTH_MAP = [
            64 * 32 * 4 => 64,
            64 * 64 * 4 => 64,
            128 * 128 * 4 => 128
        ];
    
        public const SKIN_HEIGHT_MAP = [
            64 * 32 * 4 => 32,
            64 * 64 * 4 => 64,
            128 * 128 * 4 => 128
        ];

        public function __construct()
        {
            parent::__construct('skin', 'Сменить свой скин на другой', '/skin');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (isset($args[0])) {

                    // if ($args[0] == 'reset') {

                    //     $player->changeSkin($player->getSkin(), 'new_' . mt_rand(1, 10000), 'old_' . mt_rand(1, 10000));
                    //     $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы вернули §bсобственный скин§r при помощи команды.');
                    //     return true;
                    // }

                    if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы пытаетесь §bсменить§r собственный скин.');
                            
                        } else {

                            $fileName = $target->getLowerCaseName() . '_' . mt_rand(00001, 100000) . '.png';

                            $skinData = $target->getSkin()->getSkinData();
                            $savePath = Core::getAPI()->getDataFolder() . '/skins/' . $fileName;

                            $image = self::skinDataToImage($skinData);
                            imagepng($image, $savePath);
                            imagedestroy($image);

                            Core::getAPI()->getServer()->getLogger()->notice('Скин игрока §f' . $target->getName() . ' §bзагружен под именем ' . $fileName);
                            
                            $player->changeSkin($target->getSkin(), 'new_' . mt_rand(1, 10000), 'old_' . mt_rand(1, 10000));
                            $target->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rскопировал Ваш скин себе.');
                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы скопировали скин §b' . $target->getName() . ' §rпри помощи команды.');

                        }
                        
                    } else {
                        
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');
                        
                    }
                    
                } else {
                    
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы §bскопировать§r скинь другого игрока, используйте: /skin <§bигрок§r>');
                    // $player->sendMessage(Core::getAPI()->getPrefix() . 'Вернуть собственный скин: /skin §breset');
                    
                }
                    
            }

            return true;
        }

        protected static function skinDataToImage(string $skinData) {
            $size = strlen($skinData);
            if (!in_array($size, self::ACCEPTED_SKIN_SIZES)) {
                throw new Exception('Неправильный размер');
            }
            $width = self::SKIN_WIDTH_MAP[$size];
            $height = self::SKIN_HEIGHT_MAP[$size];
            $skinPos = 0;
            $image = imagecreatetruecolor($width, $height);
            if ($image === false) {
                throw new Exception('Не получилось сохранить скин');
            }
            // Make background transparent
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $r = ord($skinData[$skinPos]);
                    $skinPos++;
                    $g = ord($skinData[$skinPos]);
                    $skinPos++;
                    $b = ord($skinData[$skinPos]);
                    $skinPos++;
                    $a = 127 - intdiv(ord($skinData[$skinPos]), 2);
                    $skinPos++;
                    $col = imagecolorallocatealpha($image, $r, $g, $b, $a);
                    imagesetpixel($image, $x, $y, $col);
                }
            }
            imagesavealpha($image, true);
            return $image;
        }
    
    }

?>