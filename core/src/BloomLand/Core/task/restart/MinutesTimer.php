<?php


namespace kingcore\iteplenky\task\restart;

    
    use kingcore\iteplenky\Loader;

    use pocketmine\scheduler\Task;

    class MinutesTimer extends Task
    {
        private $plugin;
        
        private $left = 119;

        public function __construct(Loader $plugin)
        {
            $this->plugin = $plugin;
        }

        public function onRun() : void
        {
            $this->left--;

            switch ($this->left) {
                case 14:
                    $this->plugin->getServer()->broadcastMessage(" §r> Сервер будет §cперезагружен§r через§a 15§r минут");
                    break;
                
                case 9:
                    $this->plugin->getServer()->broadcastMessage(" §r> Сервер будет §cперезагружен§r через§6 10§r минут");
                    break;
                
                case 4:
                    $this->plugin->getServer()->broadcastMessage(" §r> Сервер будет §cперезагружен§r через§c 5§r минут");
                    break;

                case 0:
                    $this->getHandler()->cancel();
                    $this->plugin->getScheduler()->scheduleRepeatingTask(new SecoundsTimer($this->plugin), 20);
                    break;
            }
            
        }
    }

?>
