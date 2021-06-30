<?php

namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
    
    use pocketmine\scheduler\Task;

    class BonusTask extends Task
    {
        private $bonus;

        public function __construct(int $bonus = 100)
        {
            $this->bonus = $bonus;
        }

        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }

        public function getBonus() : int 
        {
            return $this->bonus;
        }

        public function onRun() : void
        {
            $players = $this->getPlugin()->getServer()->getOnlinePlayers();

            if (count($players) == 0) return;
            
            $coins = 0;

            foreach ($players as $_ => $player) {
                
                $player->addMoney($this->getBonus());

                $coins += $player->getMoney();
            
                $player->sendMessage($this->getPlugin()->getPrefix() . 'Вы получили §b' . $this->getBonus() . '§r монет.');
                $player->sendMessage($this->getPlugin()->getPrefix() . 'Чтобы получить еще его раз, необходимо провести на сервере §b30 минут§r.');
            }

            $this->getPlugin()->getLogger()->notice(count($players) . ' получили бонус в размере ' . $this->getBonus() . '. Общий баланс игроков: ' . $coins . ' монет.');
        }

    }

?>
