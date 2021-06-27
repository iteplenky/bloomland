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

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if (isset($args[0])) {

                    if (($target = $this->getPlugin()->getServer()->getPlayerByPrefix($args[0])) instanceof BLPlayer) {

                        if ($target->getLowerCaseName() == $player->getLowerCaseName()) {

                            $player->sendMessage($prefix . 'Вы пытаетесь §bотправить сообщение§r самому себе§r.');
                            
                        } else {

                            $name = array_shift($args);

                            if (empty($args[0])) {
                                
                                $player->sendMessage($prefix . 'Вы отправили §cпустое сообщение§r игроку §b' . $target->getName() . '§r.');
                                $target->sendMessage(PHP_EOL . $prefix . 'Игрок §b' . $player->getName() . ' §rотправил Вам §cпустое сообщение§r. ' . 
                                PHP_EOL . $prefix .'Чтобы §bответить §rна него, используйте: /reply <...§bсообщение§r>');
                                
                            } else {

                                $target->sendMessage(PHP_EOL . $prefix . 'Игрок §b' . $player->getName() . ' §rотправил Вам §e' . implode(" ", $args) . '§r. ' . 
                                PHP_EOL . $prefix .'Чтобы §bответить §rна него, используйте: /reply <...§bсообщение§r>');
                                $player->sendMessage($prefix . 'Вы отправили §e' . implode(" ", $args) . ' §rигроку §b' . $target->getName() . '§r.');

                            }
                            
                            $player->setInterlocutor($target->getLowerCaseName()); $target->setInterlocutor($player->getLowerCaseName());

                        }
                        
                    } else {
                        
                        $player->sendMessage($prefix . 'Игрок сейчас §cне в игре§r.');
                        
                    }
                    
                } else {
                    
                    $player->sendMessage($prefix . 'Чтобы отправить §bличное сообщение§r, используйте: /tell <§cигрок§r> <§7...§bсообщение§r>');
                    
                }
                    
            }

            return true;
        }

    }

?>
