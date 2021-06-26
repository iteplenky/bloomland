<?php


namespace BloomLand\Core\commands\player;


    use BloomLand\Core\Core;
    use BloomLand\Core\BLPlayer;

    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class AfkCommand extends Command
    {
        public function __construct()
        {
            parent::__construct('afk', 'Войти в режим АФК', '/afk');
        }

        public function execute(CommandSender $player, string $label, array $args): bool
        {
            if (Core::getAPI()->isEnabled()) {

                if ($player instanceof BLPlayer) {

                    if (isset(Core::getAPI()->afk[$player->getLowerCaseName()]) and Core::getAPI()->afk[$player->getLowerCaseName()] == 1) {

                        unset(Core::getAPI()->afk[$player->getLowerCaseName()]);

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы вышли из режима §bАФК§r.');
                        
                    } else {

                        Core::getAPI()->afk[$player->getLowerCaseName()] = 1;

                        $player->sendMessage(Core::getAPI()->getPrefix() . 'Вы вошли в режим §bАФК§r.');

                    }

                }

            }

            return true;
        }

    }

?>