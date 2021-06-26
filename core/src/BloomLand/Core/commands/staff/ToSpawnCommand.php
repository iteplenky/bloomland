<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    use BloomLand\Core\utils\API;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    use pocketmine\network\mcpe\protocol\OnScreenTextureAnimationPacket;

    class ToSpawnCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('tospawn', 'Переместиться на место появления', '/tospawn', ['tpspawn']);
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (isset($args[0])) {

                        if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {
    
                            if ($target->getLowerCaseName() == $player->getLowerCaseName()) {
    
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы переместили §bсамого себя§r на спавн§r.');
                                self::teleport($player);
                                
                            } else {
    
                                $target->sendMessage(Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rперенес вас на точку §bвозрождения§r при помощи команды.');
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы перенесли §b' . $target->getName() . ' §rна точку §bвозрождения §rпри помощи команды.');
                                self::teleport($target);
                            }
                            
                        } else {
                            
                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');
                            
                        }
                        
                    } else {
                        
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы переместестить §bдругого игрока§r на спавн, используйте: /tpspawn <§bигрок>§r');
                        
                    }

                } else {

                    if (isset($args[0])) {

                        if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                            Core::getAPI()->getServer()->getLogger()->notice('Игрок ' . $target->getName() . ' перенесен в точку возраждения.');
                            self::teleport($target);

                        }  else {
                            
                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');
                            
                        }

                    }

                }

            }

            return true;
        }

        public static function teleport(BLPlayer $player): void 
        {
            $config = Core::getAPI()->getConfig();

		    if ($config->exists('spawnPosition')) {
                
                $pos = API::unpackRawLocation($config->get('spawnPosition'));

                $player->teleport($pos);

                $player->sendMessage(Core::getPrefix() . $player->translate('spawn.teleport.message'));
                $player->sendTitle($player->translate('spawn.teleport.title'), $player->translate('spawn.teleport.subtitle'), 5, 15, 5);

                $pk = new OnScreenTextureAnimationPacket();
                $pk->effectId = 29; // or 27
                $player->getNetworkSession()->sendDataPacket($pk);

            } else 
                $player->sendMessage(Core::getPrefix() . $player->translate('spawn.teleport.failed'));
        }
    }

?>