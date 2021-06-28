<?php


namespace kingcore\iteplenky\task\restart;


    use kingcore\iteplenky\Loader;

    use pocketmine\scheduler\Task;
    use pocketmine\command\ConsoleCommandSender;

    class SecoundsTimer extends Task
    {
        private $plugin;
        
        private $left = 60;

        public function __construct(Loader $plugin)
        {
            $this->plugin = $plugin;
        }

        public function onRun() : void
        {
            $this->left--;

            if($this->left <= 10 && $this->plugin->check == false) {
                $this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), "save-all");
                $this->plugin->check = true;
            }

            $this->plugin->getServer()->broadcastPopup("до перезагрузки §c" . $this->left ."§r секунд");
            
            if ($this->left <= 0) {

                $this->getHandler()->cancel();
               
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
                    $player->kick("Сервер перезагружается!\n \nПерезайди через§c 10 секунд§r..");
                }

                sleep(0.05);
                $this->plugin->getServer()->shutdown();
            }

        }

    }

?>
