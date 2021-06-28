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

        public function getPlugin() : Core
        {
            return Core::getAPI();
        }

        public function execute(CommandSender $player, string $label, array $args) : bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $server = $this->getPlugin()->getServer();

                foreach ($server->getOnlinePlayers() as $player) {

                    $player->combatTag(false);
                    $player->save();
                    
                }
                
                foreach ($server->getWorldManager()->getWorlds() as $world)
                    $world->save(true);
                
                
                foreach ($server->getOnlinePlayers() as $player) 
                    $player->kick($player->translate('tasks.stopServer.restarting'));
                
                $server->shutdown();

            }

            return true;
        }
        
    }

?>
