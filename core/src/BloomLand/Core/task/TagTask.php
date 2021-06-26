<?php

namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
 
    use BloomLand\Core\BLPlayer;

    use pocketmine\scheduler\Task;
    use BloomLand\Core\listener\ChatManager;
    
    class TagTask extends Task
    {
        public $show = 10;

        public function __construct(BLPlayer $player, string $message, ChatManager $chatManager, $show = 10)
        {
            $this->player = $player;
            $this->message = mb_strimwidth($message, 0, 18, '...');
            $this->show = $show;

            $this->chatManager = $chatManager;
        }

        public function onRun(): void
        { 
            $this->show--;

            if ($this->player instanceof BLPlayer) {

                if (in_array($this->player->getName(), $this->chatManager::$new)) {
            
                    unset($this->chatManager::$new[array_search($this->player->getName(), $this->chatManager::$new)]);
                    unset($this->chatManager::$first[array_search($this->player->getName(), $this->chatManager::$first)]);
                    $this->player->setScoreTag($this->message);
                    $this->chatManager::$tm[] = $this->player->getName();
                
                } else {

                    if (!in_array($this->player->getName(), $this->chatManager::$tm)) {

                        $this->player->setScoreTag($this->message);
                    
                    } else {

                        $this->getHandler()->cancel();

                        unset($this->chatManager::$tm[array_search($this->player->getName(), $this->chatManager::$tm)]);
                    }

                }
                
                if ($this->show <= 1) {

                    unset($this->chatManager::$new[array_search($this->player->getName(), $this->chatManager::$new)]);
                    unset($this->chatManager::$first[array_search($this->player->getName(), $this->chatManager::$first)]);
                    $this->getHandler()->cancel();

                    $this->player->setScoreTag("");
                }

            } else $this->getHandler()->cancel();
            
        }

    }

?>