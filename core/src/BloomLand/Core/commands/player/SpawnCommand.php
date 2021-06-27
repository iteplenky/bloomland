<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use BloomLand\Core\utils\API;
    
    use pocketmine\network\mcpe\protocol\OnScreenTextureAnimationPacket;

    class SpawnCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('spawn', 'Переместиться на место появления', '/spawn');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if ($player instanceof BLPlayer) {

                    if (!isset($args[0])) 
                        self::teleport($player);

                    else {

                        if ($player->isOp()) {
                
                            $config = $this->getPlugin()->getConfig();

                            switch ($args[0]) {
                                
                                case 'setpos':
                                    $config->set('spawnPosition', API::packPositionToRaw($player->getPosition()));
                                    $config->save();
                
                                    $player->sendMessage($prefix . $player->translate('spawn.manage.completed'));
                                    break;

                                case 'pos1':
                                    $config->set('spawnPos1', API::packVectorToRaw($player->getLocation()->asVector3()));

                                    $player->sendMessage($prefix . 'Первая точка указана');
                                    break;

                                case 'pos2':
                                    $config->set('spawnPos2', API::packVectorToRaw($player->getLocation()->asVector3()));
                                    $config->save();
                
                                    $player->sendMessage($prefix . 'Безопасная зона создана');
                                    break;

                                default:
                                    self::helpMessage($player);
                                    break;
                            }

                        }

                    }

                }

            }

            return true;
        }

        protected static function helpMessage(CommandSender $player): void 
        {
            $player->sendMessage($this->getPlugin()->getPrefix() . $player->translate('spawn.manage'));
        }

        public static function teleport(CommandSender $player): void 
        {
            $config = $this->getPlugin()->getConfig();

            if ($config->exists('spawnPosition')) {
                    
                    $pos = API::unpackRawLocation($config->get('spawnPosition'));

                    $player->teleport($pos);

                    $player->sendMessage($this->getPlugin()->getPrefix() . $player->translate('spawn.teleport.message'));
                    $player->sendTitle($player->translate('spawn.teleport.title'), $player->translate('spawn.teleport.subtitle'), 5, 15, 5);

                    $pk = new OnScreenTextureAnimationPacket();
                    $pk->effectId = 29; 
                    $player->getNetworkSession()->sendDataPacket($pk);

            } else {

                $player->sendMessage($this->getPlugin()->getPrefix() . $player->translate('spawn.teleport.failed'));

            }
            
        }
        
    }

?>
