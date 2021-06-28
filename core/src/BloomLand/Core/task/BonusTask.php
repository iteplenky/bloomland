<?php

namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;
    
    use pocketmine\scheduler\Task;

    class CleanerTask extends Task
    {
        private $bonus;
        private $player;

        public function __construct(BLPlayer $player, int $bonus = 100)
        {
            $this->player = $player;
            $this->bonus = $bonus;
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function getPlayer() : BLPlayer
        {
            return $player;
        }

        public function getBonus() : int 
        {
            return $this->bonus;
        }

        public function onRun() : void
        {
            if ($player->isConnected()) {

                $player-addMoney($this->getBonus());
            
                $player->sendMessage($this->getPlugin()->getPrefix() . 'Вы получили §b' . $this->getBonus() . '§r за §c30 минут§r проведенных на сервере после входа.');

            }

        }

    }

?>
