<?php


namespace BloomLand\Core\task\restart;

    
    use BloomLand\Core\Core;

    use pocketmine\scheduler\Task;

    use pocketmine\command\ConsoleCommandSender;

    class MinutesTimer extends Task
    {        
        private $left = 119;

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function onRun() : void
        {
            $this->left--;

            switch ($this->left) {
                    
                case 14:
                    $this->gePlugin()->getServer()->broadcastMessage(' §r> Сервер будет §cперезагружен§r через§a 15§r минут');
                    break;
                
                case 9:
                    $this->gePlugin()->getServer()->broadcastMessage(' §r> Сервер будет §cперезагружен§r через§6 10§r минут');
                    break;
                
                case 4:
                    $this->gePlugin()->getServer()->broadcastMessage(' §r> Сервер будет §cперезагружен§r через§c 5§r минут');
                    break;

                case 0:
                    $this->getHandler()->cancel();
                    $this->gePlugin()->getScheduler()->scheduleDelayedTask(new SecoundsTimer(), 20);
                    break;
            }
            
        }
        
    }

    class SecoundsTimer extends Task
    {        
        private $left = 60;

        public function __construct()
        {
            $this->getPlugin()->getScheduler()->scheduleRepeatingTask($this, 20);
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function onRun() : void
        {
            $this->left--;

            if ($this->left <= 10) {

                $this->gePlugin()->getServer()->dispatchCommand(new ConsoleCommandSender(), 'save-all');

            }

            $this->gePlugin()->getServer()->broadcastPopup('§fДо перезагрузки §c' . $this->left .'§r секунд');
            
            if ($this->left <= 0) {

                $this->getHandler()->cancel();
               
                foreach ($this->gePlugin()->getServer()->getOnlinePlayers() as $player) {

                    $player->kick('Сервер перезагружается!\n \nПерезайди через§c 10 секунд§r..');
                
                }

                sleep(0.05);

                $this->gePlugin()->getServer()->shutdown();
            }

        }

    }

?>
