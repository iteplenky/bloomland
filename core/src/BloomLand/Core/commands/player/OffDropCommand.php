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

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (isset(Core::getAPI()->offdrop[$player->getLowerCaseName()]) and Core::getAPI()->offdrop[$player->getLowerCaseName()] == 1) {

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы теперь §bне можете§r поднимать ресурсы с земли.');
                        
                    } else {

                        Core::getAPI()->offdrop[$player->getLowerCaseName()] = 1;

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы теперь§b можете§r поднимать ресурсы с земли.');

                    }

                }

            }

            return true;
        }

    }

?>