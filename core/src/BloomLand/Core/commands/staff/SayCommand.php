<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\Server;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class SayCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('say', 'Оповестить всех игроков', '/say');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                if (isset($args[0])) {
                    $msg = implode(' ', $args);
                    Server::getInstance()->broadcastMessage(' §c§l? §r> Игрок §c' . $player->getName() . '§r вещает: §e' . $msg);
                }
                else $player->sendMessage(' §r> Чтобы §bотправить§r всем свое сообщение оно должно иметь какое-то§c содержание§r.');
            }

            return true;
        }
    }

?>