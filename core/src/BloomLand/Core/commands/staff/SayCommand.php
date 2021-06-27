<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class SayCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('say', 'Оповестить всех игроков', '/say', ['bc', 'broadcast']);
        }

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                if (isset($args[0])) {
                
                    $msg = implode(' ', $args);
                    $this->getPlugin()->getServer()->broadcastMessage(' §c§l? §r> Игрок §c' . $player->getName() . '§r вещает: §e' . $msg);
                
                }

                else {

                    $player->sendMessage($this->getPlugin()->getPrefix() . 'Чтобы §bотправить§r всем свое сообщение оно должно иметь какое-то§c содержание§r.');

                } 

            }

            return true;
        }

    }

?>
