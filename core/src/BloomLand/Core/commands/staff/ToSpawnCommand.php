<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use BloomLand\Core\utils\API;
    use pocketmine\network\mcpe\protocol\OnScreenTextureAnimationPacket;

    class ToSpawnCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('tospawn', 'Переместиться на место появления', '/tospawn', ['tpspawn']);
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if ($player instanceof BLPlayer) {

                    if (isset($args[0])) {

                        if (($target = $this->getPlugin()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {
    
                            if ($target->getLowerCaseName() == $player->getLowerCaseName()) {
    
                                $player->sendMessage($prefix . 'Вы переместили §bсамого себя§r на спавн§r.');
                                $this->teleport($player);
                                
                            } else {
    
                                $target->sendMessage($prefix . 'Игрок §b' . $player->getName() . ' §rперенес вас на точку §bвозрождения§r при помощи команды.');
                                $player->sendMessage($prefix . 'Вы перенесли §b' . $target->getName() . ' §rна точку §bвозрождения §rпри помощи команды.');
                                $this->teleport($target);
                            }
                            
                        } else {
                            
                            $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');
                            
                        }
                        
                    } else {
                        
                        $player->sendMessage($prefix . 'Чтобы переместестить §bдругого игрока§r на спавн, используйте: /tospawn <§bигрок>§r');
                        
                    }

                } else {

                    if (isset($args[0])) {

                        if (($target = $this->getPlugin()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                            $this->getPlugin()->getServer()->getLogger()->notice('Игрок ' . $target->getName() . ' перенесен в точку возраждения.');
                            $this->teleport($target);

                        }  else {
                            
                            $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');
                            
                        }

                    }

                }

            }

            return true;
        }

        public function teleport(BLPlayer $player) : void 
        {
            $config = $this->getPlugin()->getConfig();

            if ($config->exists('spawnPosition')) {
                
                $pos = API::unpackRawLocation($config->get('spawnPosition'));

                $player->teleport($pos);

                $player->sendMessage($this->getPlugin()->getPrefix() . $player->translate('spawn.teleport.message'));
                $player->sendTitle($player->translate('spawn.teleport.title'), $player->translate('spawn.teleport.subtitle'), 5, 15, 5);

                $pk = new OnScreenTextureAnimationPacket();
                $pk->effectId = 29; // or 27
                $player->getNetworkSession()->sendDataPacket($pk);

            } else {

                $player->sendMessage($this->getPlugin()->getPrefix() . $player->translate('spawn.teleport.failed'));

            }
        
        }
    
    }

?>
