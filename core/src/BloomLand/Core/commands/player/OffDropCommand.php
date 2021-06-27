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
        
        // move function to BLPlayer.php

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {
                    
                    if (isset(Core::getAPI()->offdrop[$player->getLowerCaseName()])) {
                        
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы теперь§b можете§r поднимать ресурсы с земли.');
                        // unset(Core::getAPI()->offdrop[$player->getLowerCaseName()]);
                        
                    } else {
                     
                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы теперь §bне можете§r поднимать ресурсы с земли.');
                        // Core::getAPI()->offdrop[$player->getLowerCaseName()] = 1;
                        
                    }

                }

            }

            return true;
        }

    }

?>
