<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class TellCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('tell', 'Тайное общение друг с другом', '/tell', ["msg", "whisper", "w"]);
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (isset($args[0])) {

                    if (($target = Core::getAPI()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы пытаетесь §bотправить сообщение§r самому себе§r.');
                            
                        } else {

                            $name = array_shift($args);

                            if (empty($args[0])) {
                                
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы отправили §cпустое сообщение§r игроку §b' . $target->getName() . '§r.');
                                $target->sendMessage(PHP_EOL . Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rотправил Вам §cпустое сообщение§r. ' . 
                                PHP_EOL . Core::getAPI()->getPrefix() .'Чтобы §bответить §rна него, используйте: /reply <...§bсообщение§r>');
                                
                            } else {

                                $target->sendMessage(PHP_EOL . Core::getAPI()->getPrefix() . 'Игрок §b' . $player->getName() . ' §rотправил Вам §e' . implode(" ", $args) . '§r. ' . 
                                PHP_EOL . Core::getAPI()->getPrefix() .'Чтобы §bответить §rна него, используйте: /reply <...§bсообщение§r>');
                                $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы отправили §e' . implode(" ", $args) . ' §rигроку §b' . $target->getName() . '§r.');

                            }

                            Core::getAPI()->setLastTalkers($player->getLowerCaseName(), $target->getLowerCaseName());

                        }
                        
                    } else {
                        
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Игрок сейчас §cне в игре§r.');
                        
                    }
                    
                } else {
                    
                    $player->sendMessage(Core::getAPI()->getPrefix() . 'Чтобы отправить §bличное сообщение§r, используйте: /tell <§cигрок§r> <§7...§bсообщение§r>');
                    
                }
                    
            }

            return true;
        }
    }

?>