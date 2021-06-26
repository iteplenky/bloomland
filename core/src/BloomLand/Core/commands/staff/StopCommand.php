<?php


namespace BloomLand\Core\commands\staff;


    use BloomLand\Core\Core;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class StopCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('stop', 'Управление сервером', '/stop');
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if (Core::getAPI()->isEnabled()) {

                $server = Core::getAPI()->getServer();

                foreach($server->getOnlinePlayers() as $player)
                    $player->save();
                
                foreach($server->getWorldManager()->getWorlds() as $world)
                    $world->save(true);
                
                
                foreach ($server->getOnlinePlayers() as $player) 
                    $player->kick($player->translate('tasks.stopServer.restarting'));
                
                $server->shutdown();

            }

            return true;
        }
    }

?>