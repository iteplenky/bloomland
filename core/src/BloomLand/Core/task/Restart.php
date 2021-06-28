<?php


namespace BloomLand\Core\task;

    
    use BloomLand\Core\Core;

    use pocketmine\scheduler\Task;

    class Restart extends Task
    {        
        private $left = 120;

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function onRun() : void
        {
            $this->left--;

            $prefix = $this->getPlugin()->getPrefix();

            switch ($this->left) {

                case 60:
                    $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Сервер будет §cперезагружен§r через§a 60§r минут.');
                    break;

                case 30:
                    $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Сервер будет §cперезагружен§r через§a 30§r минут.');
                    break;
                    
                case 15:
                    $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Сервер будет §cперезагружен§r через§a 15§r минут.');
                    break;
                
                case 10:
                    $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Сервер будет §cперезагружен§r через§6 10§r минут.');
                    break;
            
                case 5:
                    $this->getPlugin()->getServer()->broadcastMessage($prefix . 'Сервер будет §cперезагружен§r через§c 5§r минут.');
                    break;

                case 0:
                    $this->getHandler()->cancel();
                    $this->getPlugin()->getScheduler()->scheduleRepeatingTask(new SecoundsTimer(), 20);
                    break;
            }
            
        }
        
    }

    class SecoundsTimer extends Task
    {        
        private $left = 60;

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function onRun() : void
        {
            $this->left--;

            $server = $this->getPlugin()->getServer();


            if ($this->left <= 5) {

                foreach ($server->getOnlinePlayers() as $player) {

                    $player->save();
                                    
                }

                foreach ($server->getWorldManager()->getWorlds() as $world) {

                    $world->save(true);

                }
                
            }

            $server->broadcastPopup('§fДо перезагрузки §c' . $this->left . '§f секунд.');
            
            if ($this->left <= 0) {

                $this->getHandler()->cancel();
               
                foreach ($server->getOnlinePlayers() as $player) {

                    $player->combatTag(false);
                   
                    $player->kick($player->translate('tasks.stopServer.restarting'));
                
                }

                sleep(0.05);

                $server->shutdown();
            }

        }

    }

?>
