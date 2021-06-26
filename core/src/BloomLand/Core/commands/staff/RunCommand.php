<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;
    use BloomLand\Core\task\RunTask;

    use pocketmine\Server;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class RunCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('run', 'Начать игру', '/run');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            $prefix = ' §c§lПОБЕГ §r> ';
            if (Core::getAPI()->isEnabled()) {

                if (count(Core::getAPI()->getServer()->getOnlinePlayers()) >= 3) {

                    Core::getAPI()->getServer()->broadcastMessage($prefix . 'Игра скоро начнется!..');
                    
                    Core::getAPI()->getServer()->broadcastTitle('§b§lзагрузка..', '', 5, 20, 10);

                    new RunTask($player);
                    
                } else {

                    Core::getAPI()->getServer()->broadcastMessage($prefix . 'Игра не может начаться, потому что игроков только §b' . count(Core::getAPI()->getServer()->getOnlinePlayers()) . '§r, а надо от §b3§r.');
                }

            }

            return true;
        }
    }

?>