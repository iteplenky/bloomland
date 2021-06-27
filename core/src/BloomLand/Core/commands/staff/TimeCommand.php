<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class TimeCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('time', 'Управление временем в мире', '/time');
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if (isset($args[0])) {

                    $server = $this->getPlugin()->getServer();
                
                    $world = $server->getWorldManager()->getWorldByName('world');
                    
                    if (is_numeric($args[0])) {

                        $world->setTime((int) $args[0]);
                        $server->broadcastMessage($prefix . 'Время в мире было изменено игроком §b' . $player->getName() . ' §rна §d' . $args[0] . '§r.');
                    
                    } else {

                        switch ($args[0]) {
                            case 'day':
                                $world->setTime(6000);
                                $server->broadcastMessage($prefix . 'Время в мире было изменено игроком §b' . $player->getName() . ' §rна §6день§r.');
                                break;

                            case 'night':
                                $world->setTime(14000);
                                $server->broadcastMessage($prefix . 'Время в мире было изменено игроком §b' . $player->getName() . ' §rна §9ночь§r.');
                                break;
                            
                            case 'set':

                                if (isset($args[1])) {

                                    if (is_numeric($args[0])) {

                                        $world->setTime((int) $args[0]);
                                        $server->broadcastMessage($prefix . 'Время в мире было изменено игроком §b' . $player->getName() . ' §rна §d' . $args[0] . '§r.');
                                        return true;

                                    }

                                    switch ($args[1]) {
                                        case 'day':
                                            $world->setTime(6000);
                                            $server->broadcastMessage($prefix . 'Время в мире было изменено игроком §b' . $player->getName() . ' §rна §6день§r.');
                                            break;
                                        
                                        case 'night':
                                            $world->setTime(14000);
                                            $server->broadcastMessage($prefix . 'Время в мире было изменено игроком §b' . $player->getName() . ' §rна §9ночь§r.');
                                            break;
                                        
                                        default:
                                            $player->sendMessage($prefix . 'Убедитесь, что Вы правильно вводите §bвремя суток§r.');
                                            break;
                                    }

                                }
                                break;
                            
                            default:
                                $player->sendMessage($prefix . 'Убедитесь, что Вы правильно вводите §bвремя суток§r.');
                                break;
                        }
                    
                    }

                } else {

                    $player->sendMessage($prefix . 'Чтобы сменить §bвремя §rв игре используйте: §b/time §r<§bday§r/§bnight§r/§bчисло§b>');  
                    
                } 
            }

            return true;
        }
    }

?>
