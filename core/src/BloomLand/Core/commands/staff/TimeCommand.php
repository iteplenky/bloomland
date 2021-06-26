<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\Server;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class TimeCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('time', 'Управление временем в мире', '/time');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (isset($args[0])) {
                
                    $level = Server::getInstance()->getWorldManager()->getWorldByName('world');
                    
                    if (is_numeric($args[0])) {
                        $level->setTime((int) $args[0]);
                        Server::getInstance()->broadcastMessage(' §r> Время в мире было изменено игроком §b' . $player->getName() . ' §rна §d' . $args[0] . '§r.');
                    } else {
                        if ($args[0] == 'day') {
                            $level->setTime(6000);
                            Server::getInstance()->broadcastMessage(' §r> Время в мире было изменено игроком §b' . $player->getName() . ' §rна §6день§r.');
                        } else if ($args[0] == 'night') {
                            $level->setTime(16000);
                            Server::getInstance()->broadcastMessage(' §r> Время в мире было изменено игроком §b' . $player->getName() . ' §rна §9ночь§r.');
                        } else {
                            $player->sendMessage(' §r> Убедитесь, что Вы правильно вводите §bвремя суток§r.');
                        }
                    }
                } else 
                    $player->sendMessage(' §r> Чтобы сменить §bвремя §rв игре используйте: §b/time §r<§bday§r/§bnight§r/§bчисло§b>');
            }

            return true;
        }
    }

?>