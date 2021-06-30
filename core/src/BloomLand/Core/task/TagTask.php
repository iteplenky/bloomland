<?php

namespace BloomLand\Core\task;


    use BloomLand\Core\Core;
 
    use BloomLand\Core\BLPlayer;

    use pocketmine\scheduler\Task;
    use BloomLand\Core\listener\PlayerListener;
    
    class TagTask extends Task
    {
        // public $show = 10;

        public function __construct(BLPlayer $player, PlayerListener $playerListener, $show = 1)
        {
            $this->player = $player;
            $this->show = $show;

            $this->playerListener = $playerListener;
        }

        public function getHealth(BLPlayer $player) : string 
        {
            if ($player->isTagged()) {

                if ($player->getAbsorption() > 0) 
                    $mode = round($player->getHealth()) . ' ' . round($player->getAbsorption()) . '';

                else 
                    $mode = round($player->getHealth()) . '';

                return $mode;
            
            } else {

                return '';

            }

        }

        public function onRun(): void
        { 
            $this->show--;

            if ($this->player instanceof BLPlayer) {

                if (in_array($this->player->getName(), $this->playerListener::$new)) {

                    unset($this->playerListener::$new[array_search($this->player->getName(), $this->playerListener::$new)]);
                    unset($this->playerListener::$first[array_search($this->player->getName(), $this->playerListener::$first)]);
                    $this->player->setScoreTag($this->getHealth($this->player));
                    $this->playerListener::$tm[] = $this->player->getName();
                
                } else {
                
                    if (!in_array($this->player->getName(), $this->playerListener::$tm)) {

                        $this->player->setScoreTag($this->getHealth($this->player));
                    
                    } else {

                        // $this->getHandler()->cancel();

                        unset($this->playerListener::$tm[array_search($this->player->getName(), $this->playerListener::$tm)]);
                    }

                }
                
                if ($this->show <= 1) {


                    unset($this->playerListener::$new[array_search($this->player->getName(), $this->playerListener::$new)]);
                    unset($this->playerListener::$first[array_search($this->player->getName(), $this->playerListener::$first)]);
                    // $this->getHandler()->cancel();

                    // $this->player->setScoreTag("");
                }

            } 
            
        }

    }

?>