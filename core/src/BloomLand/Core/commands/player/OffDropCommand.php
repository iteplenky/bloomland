<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class OffDropCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('offdrop', 'Переместиться на место появления', '/offdrop');
        }
        
        public function getPlugin() : Core 
        {
            return Core::getAPI();
        }
        
        // move function to BLPlayer.php

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if ($this->getPlugin()->isEnabled()) {

                $prefix = $this->getPlugin()->getPrefix();

                if ($player instanceof BLPlayer) {
                    
                    if (isset($this->getPlugin()->offdrop[$player->getLowerCaseName()])) {
                        
                        $player->sendMessage($prefix . 'Вы теперь§b можете§r поднимать ресурсы с земли.');
                        // unset($this->getPlugin()\->offdrop[$player->getLowerCaseName()]);
                        
                    } else {
                     
                        $player->sendMessage($prefix . 'Вы теперь §bне можете§r поднимать ресурсы с земли.');
                        // $this->getPlugin()->offdrop[$player->getLowerCaseName()] = 1;
                        
                    }

                }

            }

            return true;
        }

    }

?>
